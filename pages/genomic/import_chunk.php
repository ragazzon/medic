<?php
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
@set_time_limit(120);
header('Content-Type: application/json');
$patientId=intval($_POST['patient_id']??0);
$offset=intval($_POST['offset']??0);
$limit=intval($_POST['limit']??50000);
$fileName=$_POST['file_name']??'';
$action=$_POST['action']??'chunk';
if(!$patientId||!canAccessPatient($patientId)){die(json_encode(['error'=>'Access denied']));}
$pdo=getConnection();
$uploadDir=__DIR__.'/../../uploads/genomic/';
if(!is_dir($uploadDir))mkdir($uploadDir,0755,true);
$filePath=$uploadDir.basename($fileName);

if($action==='upload'){
    // Save uploaded file
    if(!isset($_FILES['file'])){die(json_encode(['error'=>'No file']));}
    move_uploaded_file($_FILES['file']['tmp_name'],$filePath);
    // Count lines
    $lines=0;$h=fopen($filePath,'r');
    while(fgets($h)!==false)$lines++;
    fclose($h);
    // Create import log
    $s=$pdo->prepare("INSERT INTO genomic_imports (patient_id,file_name,status,imported_by,total_snps) VALUES (?,'processing',?,0)");
    $s->execute([$patientId,$_FILES['file']['name'],getCurrentUserId()]);
    // Clear old data
    $pdo->prepare("DELETE FROM patient_genotypes WHERE patient_id=?")->execute([$patientId]);
    $pdo->prepare("DELETE FROM patient_pgx_results WHERE patient_id=?")->execute([$patientId]);
    echo json_encode(['ok'=>true,'total_lines'=>$lines,'file_name'=>$_FILES['file']['name'],'saved_as'=>basename($filePath)]);
    exit;
}

if($action==='chunk'){
    if(!file_exists($filePath)){die(json_encode(['error'=>'File not found: '.$filePath]));}
    $h=fopen($filePath,'r');
    $lineNum=0;$imported=0;$batch=[];
    $sql="INSERT IGNORE INTO patient_genotypes (patient_id,rsid,chromosome,position,genotype) VALUES ";
    $pdo->exec("SET autocommit=0");
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
        if(count($batch)>=1000){
            $pdo->exec($sql.implode(',',$batch));
            $imported+=count($batch);$batch=[];
        }
    }
    if(!empty($batch)){$pdo->exec($sql.implode(',',$batch));$imported+=count($batch);}
    $pdo->exec("COMMIT");$pdo->exec("SET autocommit=1");
    fclose($h);
    echo json_encode(['ok'=>true,'offset'=>$offset,'limit'=>$limit,'imported'=>$imported,'lineNum'=>$lineNum]);
    exit;
}

if($action==='analyze'){
    // Update import log
    $count=$pdo->prepare("SELECT COUNT(*) FROM patient_genotypes WHERE patient_id=?");
    $count->execute([$patientId]);$total=$count->fetchColumn();
    $pdo->prepare("UPDATE genomic_imports SET status='completed',imported_snps=? WHERE patient_id=? ORDER BY imported_at DESC LIMIT 1")->execute([$total,$patientId]);
    // Run analysis
    runGenomicAnalysis($patientId);
    // Clean temp file
    if(file_exists($filePath))@unlink($filePath);
    echo json_encode(['ok'=>true,'total_snps'=>$total,'status'=>'analyzed']);
    exit;
}
echo json_encode(['error'=>'Unknown action']);
