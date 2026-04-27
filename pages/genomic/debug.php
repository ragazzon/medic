<?php
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
$patientId=intval($_GET['patient_id']??0);
if(!$patientId){die('No patient_id');}
$pdo=getConnection();
header('Content-Type: text/plain');
echo "=== PATIENT GENOTYPES DEBUG ===

";

// Count total
$s=$pdo->prepare("SELECT COUNT(*) FROM patient_genotypes WHERE patient_id=?");
$s->execute([$patientId]);
echo "Total SNPs: ".$s->fetchColumn()."

";

// Show first 10 rows
$s=$pdo->prepare("SELECT rsid,chromosome,position,genotype FROM patient_genotypes WHERE patient_id=? LIMIT 10");
$s->execute([$patientId]);
echo "First 10 rows:
";
foreach($s->fetchAll() as $r){echo $r['rsid']." | chr".$r['chromosome']." | pos".$r['position']." | ".$r['genotype']."
";}

// Check specific known RSIDs
echo "
=== CHECKING KEY RSIDS ===
";
$check=['rs1045642','rs4680','rs762551','rs12248560','rs1801133','rs6025','rs3892097','rs2231142'];
foreach($check as $rsid){
    $s=$pdo->prepare("SELECT genotype FROM patient_genotypes WHERE patient_id=? AND rsid=?");
    $s->execute([$patientId,$rsid]);
    $g=$s->fetchColumn();
    echo "$rsid: ".($g?$g:"NOT FOUND")."
";
}

// Check import log
echo "
=== IMPORT LOG ===
";
$s=$pdo->prepare("SELECT * FROM genomic_imports WHERE patient_id=? ORDER BY imported_at DESC LIMIT 3");
$s->execute([$patientId]);
foreach($s->fetchAll() as $r){echo $r['file_name']." | ".$r['status']." | ".$r['imported_snps']." SNPs | ".$r['imported_at']."
";}

// Check pgx_rules count
echo "
=== PGX RULES ===
";
$s=$pdo->query("SELECT COUNT(*) FROM pgx_rules WHERE is_active=1");
echo "Active rules: ".$s->fetchColumn()."
";

// Check analysis results
echo "
=== ANALYSIS RESULTS ===
";
$s=$pdo->prepare("SELECT COUNT(*) FROM patient_pgx_results WHERE patient_id=?");
$s->execute([$patientId]);
echo "Results: ".$s->fetchColumn()."
";
$s=$pdo->prepare("SELECT rsid,patient_genotype,status,phenotype FROM patient_pgx_results WHERE patient_id=? AND status!='unknown' LIMIT 10");
$s->execute([$patientId]);
foreach($s->fetchAll() as $r){echo $r['rsid']." | ".$r['patient_genotype']." | ".$r['status']." | ".$r['phenotype']."
";}
