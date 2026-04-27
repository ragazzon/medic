<?php
error_reporting(0); // Suppress PHP errors in JSON output
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
@set_time_limit(120);
@ini_set('display_errors',0);
header('Content-Type: application/json');

try {
$patientId=intval($_POST['patient_id']??0);
$offset=intval($_POST['offset']??0);
$limit=intval($_POST['limit']??50000);
$fileName=$_POST['file_name']??'';
$action=$_POST['action']??'chunk';

if(!$patientId||!canAccessPatient($patientId)){
    echo json_encode(['error'=>'Access denied']);exit;
}
$pdo=getConnection();
$uploadDir=__DIR__.'/../../uploads/genomic/';
if(!is_dir($uploadDir)){@mkdir($uploadDir,0777,true);}
$filePath=$uploadDir.preg_replace('/[^a-zA-Z0-9._-]/','',basename($fileName));

if($action==='upload'){
    if(!isset($_FILES['file'])||$_FILES['file']['error']!==UPLOAD_ERR_OK){
        echo json_encode(['error'=>'Upload error: '.($_FILES['file']['error']??'no file')]);exit;
    }
    $safeName='genomic_'.time().'_'.$patientId.'.csv';
    $filePath=$uploadDir.$safeName;
    if(!move_uploaded_file($_FILES['file']['tmp_name'],$filePath)){
        echo json_encode(['error'=>'Failed to save file to '.$uploadDir]);exit;
    }
    $lines=0;$h=fopen($filePath,'r');
    while(fgets($h)!==false)$lines++;
    fclose($h);
    try{
        $s=$pdo->prepare("INSERT INTO genomic_imports (patient_id,file_name,status,imported_by) VALUES (?,?,'processing',?)");
        $s->execute([$patientId,$_FILES['file']['name'],getCurrentUserId()]);
    }catch(Exception $e){}
    try{
        $pdo->prepare("DELETE FROM patient_genotypes WHERE patient_id=?")->execute([$patientId]);
        $pdo->prepare("DELETE FROM patient_pgx_results WHERE patient_id=?")->execute([$patientId]);
    }catch(Exception $e){}
    echo json_encode(['ok'=>true,'total_lines'=>$lines,'saved_as'=>$safeName]);
    exit;
}

if($action==='chunk'){
    if(!file_exists($filePath)){
        echo json_encode(['error'=>'File not found. Try re-uploading.']);exit;
    }
    $h=fopen($filePath,'r');
    $lineNum=0;$imported=0;$batch=[];
    $sql="INSERT IGNORE INTO patient_genotypes (patient_id,rsid,chromosome,position,genotype) VALUES ";
    try{$pdo->exec("SET autocommit=0");}catch(Exception $e){}
    while(($line=fgets($h))!==false){
        $lineNum++;
        if($lineNum<=$offset)continue;
        if($lineNum>$offset+$limit)break;
        $line=trim($line);
        if(empty($line)||$line[0]==='#')continue;
        if(stripos($line,'rsid')===0)continue;
        $cols=str_getcsv($line);
        if(count($cols)<4)continue;
        $rsid=trim($cols[0]);$chr=trim($cols[1]);$pos=intval($cols[2]);$geno=trim($cols[3]);
        if(empty($rsid)||empty($geno)||$geno==='--'||$geno==='00')continue;
        if(strpos($rsid,'rs')!==0&&strpos($rsid,'i')!==0)continue;
        $batch[]="($patientId,'".addslashes($rsid)."','".addslashes($chr)."',$pos,'".addslashes($geno)."')";
        if(count($batch)>=500){
            try{$pdo->exec($sql.implode(',',$batch));}catch(Exception $e){}
            $imported+=count($batch);$batch=[];
        }
    }
    if(!empty($batch)){try{$pdo->exec($sql.implode(',',$batch));}catch(Exception $e){}$imported+=count($batch);}
    try{$pdo->exec("COMMIT");$pdo->exec("SET autocommit=1");}catch(Exception $e){}
    fclose($h);
    echo json_encode(['ok'=>true,'offset'=>$offset,'imported'=>$imported]);
    exit;
}

if($action==='analyze'){
    $count=$pdo->prepare("SELECT COUNT(*) FROM patient_genotypes WHERE patient_id=?");
    $count->execute([$patientId]);$total=$count->fetchColumn();
    try{$pdo->prepare("UPDATE genomic_imports SET status='completed',imported_snps=? WHERE patient_id=? ORDER BY imported_at DESC LIMIT 1")->execute([$total,$patientId]);}catch(Exception $e){}
    runGenomicAnalysis($patientId);
    if(file_exists($filePath))@unlink($filePath);
    echo json_encode(['ok'=>true,'total_snps'=>intval($total)]);
    exit;
}
echo json_encode(['error'=>'Unknown action: '.$action]);
}catch(Exception $e){
    echo json_encode(['error'=>$e->getMessage()]);
}
