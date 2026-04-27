<?php
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
$pid=intval($_GET['patient_id']??0);
if(!$pid){die('No patient_id');}
$pdo=getConnection();
header('Content-Type: text/plain');
// Check ALL SNPs that have rules
$rules=$pdo->query("SELECT rsid,gene_symbol,ref_genotype,het_genotypes,risk_genotypes FROM pgx_rules WHERE is_active=1 ORDER BY gene_symbol")->fetchAll();
echo "RSID|GENE|GENERA_GENO|REF|HET|RISK|MATCH\n";
echo str_repeat('-',100)."\n";
foreach($rules as $r){
    $s=$pdo->prepare("SELECT genotype FROM patient_genotypes WHERE patient_id=? AND rsid=?");
    $s->execute([$pid,$r['rsid']]);
    $geno=$s->fetchColumn()?:'N/D';
    $rev=strlen($geno)==2?$geno[1].$geno[0]:$geno;
    $refs=explode(',',$r['ref_genotype']);
    $hets=explode(',',$r['het_genotypes']);
    $risks=explode(',',$r['risk_genotypes']);
    $match='NONE';
    if(in_array($geno,$risks)||in_array($rev,$risks))$match='RISK';
    elseif(in_array($geno,$hets)||in_array($rev,$hets))$match='HET';
    elseif(in_array($geno,$refs)||in_array($rev,$refs))$match='REF';
    elseif($geno==='N/D')$match='N/D';
    echo sprintf("%-14s %-10s %-6s %-8s %-8s %-8s %s\n",$r['rsid'],$r['gene_symbol'],$geno,$r['ref_genotype'],$r['het_genotypes'],$r['risk_genotypes'],$match);
}
