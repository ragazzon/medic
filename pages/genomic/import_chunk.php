<?php
error_reporting(0);
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
@set_time_limit(120);
@ini_set('display_errors',0);
header('Content-Type: application/json');

try {
$patientId=intval($_POST['patient_id']??0);
$action=$_POST['action']??'';
if(!$patientId||!canAccessPatient($patientId)){echo json_encode(['error'=>'Acesso negado']);exit;}
$pdo=getConnection();

// Use same upload dir pattern as rest of system
$uploadDir=__DIR__.'/../../uploads/genomic/';
if(!is_dir($uploadDir))@mkdir($uploadDir,0755,true);

if($action==='upload'){
    if(!isset($_FILES['file'])||$_FILES['file']['error']!==UPLOAD_ERR_OK){
        $errs=[0=>'OK',1=>'Arquivo muito grande (php.ini)',2=>'Arquivo muito grande (form)',3=>'Upload parcial',4=>'Nenhum arquivo',6=>'Sem pasta temp',7=>'Falha escrita'];
        echo json_encode(['error'=>'Upload: '.($errs[$_FILES['file']['error']??4]??'Erro '.$_FILES['file']['error'])]);exit;
    }
    $safeName='gen_'.$patientId.'_'.time().'.csv';
    $dest=$uploadDir.$safeName;
    if(!move_uploaded_file($_FILES['file']['tmp_name'],$dest)){
        // Try alternate location
        $altDir=sys_get_temp_dir().'/';
        $dest=$altDir.$safeName;
        if(!move_uploaded_file($_FILES['file']['tmp_name'],$dest)){
            echo json_encode(['error'=>'Nao conseguiu salvar. uploadDir='.$uploadDir.' altDir='.$altDir]);exit;
        }
    }
    $lines=0;$h=fopen($dest,'r');while(fgets($h)!==false)$lines++;fclose($h);
    try{$pdo->prepare("INSERT INTO genomic_imports (patient_id,file_name,status,imported_by) VALUES (?,?,'processing',?)")->execute([$patientId,$_FILES['file']['name'],getCurrentUserId()]);}catch(Exception $e){}
    try{$pdo->prepare("DELETE FROM patient_genotypes WHERE patient_id=?")->execute([$patientId]);
        $pdo->prepare("DELETE FROM patient_pgx_results WHERE patient_id=?")->execute([$patientId]);}catch(Exception $e){}
    echo json_encode(['ok'=>true,'total_lines'=>$lines,'saved_as'=>$safeName,'path'=>$dest]);exit;
}

$fileName=$_POST['file_name']??'';
$filePath=$uploadDir.preg_replace('/[^a-zA-Z0-9._-]/','',basename($fileName));
// Also check temp dir
if(!file_exists($filePath))$filePath=sys_get_temp_dir().'/'.basename($fileName);

if($action==='chunk'){
    $offset=intval($_POST['offset']??0);
    $limit=intval($_POST['limit']??50000);
    if(!file_exists($filePath)){echo json_encode(['error'=>'Arquivo nao encontrado: '.basename($fileName)]);exit;}
    $h=fopen($filePath,'r');$lineNum=0;$imported=0;$batch=[];
    $sql="INSERT IGNORE INTO patient_genotypes (patient_id,rsid,chromosome,position,genotype) VALUES ";
    while(($line=fgets($h))!==false){
        $lineNum++;
        if($lineNum<=$offset)continue;
        if($lineNum>$offset+$limit)break;
        $line=trim($line);
        if(empty($line)||$line[0]==='#'||stripos($line,'rsid')===0)continue;
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
    fclose($h);
    echo json_encode(['ok'=>true,'offset'=>$offset,'imported'=>$imported]);exit;
}

if($action==='analyze'){
    $count=$pdo->prepare("SELECT COUNT(*) FROM patient_genotypes WHERE patient_id=?");
    $count->execute([$patientId]);$total=$count->fetchColumn();
    try{$pdo->prepare("UPDATE genomic_imports SET status='completed',imported_snps=? WHERE patient_id=? ORDER BY imported_at DESC LIMIT 1")->execute([$total,$patientId]);}catch(Exception $e){}
    runGenomicAnalysis($patientId);
    if(file_exists($filePath))@unlink($filePath);
    echo json_encode(['ok'=>true,'total_snps'=>intval($total)]);exit;
}
echo json_encode(['error'=>'Acao desconhecida: '.$action]);
}catch(Exception $e){echo json_encode(['error'=>$e->getMessage()]);}
