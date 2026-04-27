<?php
$pageTitle='Painel'; require_once __DIR__.'/../../includes/auth.php'; require_once __DIR__.'/../../includes/genomic.php'; requireLogin();
$patientId=intval($_GET['patient_id']??0); $panelCode=$_GET['panel']??'';
if(!$patientId||!canAccessPatient($patientId)||!$panelCode){redirect(baseUrl('pages/patients/list.php'));}
$pdo=getConnection(); $patient=$pdo->prepare('SELECT * FROM patients WHERE id=?'); $patient->execute([$patientId]); $patient=$patient->fetch();
$panel=$pdo->prepare('SELECT * FROM pgx_panels WHERE code=?'); $panel->execute([$panelCode]); $panel=$panel->fetch();
if(!$panel){redirect(baseUrl('pages/genomic/dashboard.php?patient_id='.$patientId));}
$results=getPanelResults($patientId,$panelCode); require_once __DIR__.'/../../includes/header.php'; ?>
<div class='page-header'><h1><i class='bi <?=$panel['icon']?> me-2'></i><?=sanitize($panel['name'])?> - <?=sanitize($patient['name'])?></h1>
<a href='<?=baseUrl('pages/genomic/dashboard.php?patient_id='.$patientId)?>' class='btn btn-outline-secondary'><i class='bi bi-arrow-left me-1'></i>Dashboard</a></div>
<?php if(empty($results)):?><div class='alert alert-info'>Nenhum resultado.</div><?php else:?>
<div class='row g-3'><?php foreach($results as $r):?>
<div class='col-md-6 col-xl-4'><div class='card h-100 border-start border-4 <?=($r['status']??'')==='risk'?'border-danger':(($r['status']??'')==='attention'?'border-warning':'border-success')?>'>
<div class='card-body'><div class='d-flex justify-content-between mb-2'>
<div><h6 class='mb-0'><?=$r['gene_symbol']?></h6><small class='text-muted'><?=$r['gene_name']??$r['variant_name']?></small></div>
<?=genomicStatusBadge($r['status']??'unknown')?></div>
<table class='table table-sm mb-2'><tr><td class='text-muted'>SNP</td><td><code><?=$r['rsid']?></code></td></tr>
<tr><td class='text-muted'>Genotipo</td><td><b><?=$r['patient_genotype']??'N/D'?></b></td></tr>
<tr><td class='text-muted'>Fenotipo</td><td><?=sanitize($r['phenotype']??'')?></td></tr>
<tr><td class='text-muted'>Evidencia</td><td><span class='badge bg-secondary'><?=$r['evidence_level']?></span></td></tr></table>
<?php if($r['interpretation']):?><div class='alert alert-light mb-0 py-2 px-3'><small><?=sanitize($r['interpretation'])?></small></div><?php endif;?>
</div></div></div><?php endforeach;?></div><?php endif;?>
<?php require_once __DIR__.'/../../includes/footer.php';?>
