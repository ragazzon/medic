<?php
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
$patientId=intval($_GET['patient_id']??0);
if(!$patientId){die('No patient_id');}
$pdo=getConnection();
echo "<h2>Re-analyzing patient $patientId...</h2>";

// Run analysis
runGenomicAnalysis($patientId);

// Show results
$s=$pdo->prepare("SELECT pr.rsid,pr.patient_genotype,pr.status,pr.phenotype,r.gene_symbol,r.ref_genotype,r.het_genotypes,r.risk_genotypes FROM patient_pgx_results pr JOIN pgx_rules r ON pr.rule_id=r.id WHERE pr.patient_id=? ORDER BY pr.status,r.gene_symbol");
$s->execute([$patientId]);
$results=$s->fetchAll();
echo "<p>Total results: ".count($results)."</p>";
echo "<table border=1 cellpadding=4><tr><th>Gene</th><th>rsid</th><th>Genotype</th><th>Status</th><th>Phenotype</th><th>Ref</th><th>Het</th><th>Risk</th></tr>";
foreach($results as $r){
    $bg=$r['status']==='risk'?'#fcc':($r['status']==='attention'?'#ffc':($r['status']==='normal'?'#cfc':'#eee'));
    echo "<tr style='background:$bg'><td>{$r['gene_symbol']}</td><td>{$r['rsid']}</td><td><b>{$r['patient_genotype']}</b></td><td>{$r['status']}</td><td>{$r['phenotype']}</td><td>{$r['ref_genotype']}</td><td>{$r['het_genotypes']}</td><td>{$r['risk_genotypes']}</td></tr>";
}
echo "</table>";
echo "<p><a href='".baseUrl('pages/genomic/dashboard.php?patient_id='.$patientId)."'>Go to Dashboard</a></p>";
