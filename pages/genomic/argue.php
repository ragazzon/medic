<?php
$pageTitle='Argumente com o Medico';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
$patientId=intval($_GET['patient_id']??0);
$drugName=$_GET['drug']??null;
if(!$patientId||!canAccessPatient($patientId)){redirect(baseUrl('pages/patients/list.php'));}
$pdo=getConnection();
$patient=$pdo->prepare('SELECT * FROM patients WHERE id=?');$patient->execute([$patientId]);$patient=$patient->fetch();
$drugAnalysis=getDrugAnalysis($patientId,$drugName);
$allDrugs=getDrugAnalysis($patientId);
require_once __DIR__.'/../../includes/header.php';
?>
<div class='page-header'><h1><i class='bi bi-chat-left-quote me-2'></i>Argumente com o Medico - <?=sanitize($patient['name'])?></h1>
<div><button onclick='window.print()' class='btn btn-outline-primary btn-sm'><i class='bi bi-printer me-1'></i>Imprimir</button>
<a href='<?=baseUrl('pages/genomic/dashboard.php?patient_id='.$patientId)?>' class='btn btn-outline-secondary btn-sm'><i class='bi bi-arrow-left me-1'></i>Dashboard</a></div></div>
<div class='alert alert-info mb-4'><h6><i class='bi bi-info-circle me-1'></i>Como usar</h6><p class='mb-0'>Imprima ou mostre ao medico para embasar decisoes com dados geneticos concretos.</p></div>
<div class='card mb-3'><div class='card-body py-2'><form method='GET' class='d-flex align-items-center gap-2'>
<input type='hidden' name='patient_id' value='<?=$patientId?>'>
<label class='text-nowrap'><b>Medicamento:</b></label>
<select name='drug' class='form-select form-select-sm' onchange='this.form.submit()'>
<option value=''>-- Todos --</option>
<?php foreach($allDrugs as $d):?><option value='<?=urlencode($d['name'])?>' <?=$drugName===$d['name']?'selected':''?>><?=sanitize($d['name'])?> <?=genomicStatusIcon($d['worst_status'])?></option><?php endforeach;?>
</select></form></div></div>
<?php foreach($drugAnalysis as $drug):?>
<div class='card mb-4 border-start border-4 <?=$drug['worst_status']==='risk'?'border-danger':($drug['worst_status']==='attention'?'border-warning':'border-success')?>'>
<div class='card-header d-flex justify-content-between'><h5 class='mb-0'><?=genomicStatusIcon($drug['worst_status'])?> <?=sanitize($drug['name'])?></h5><?=genomicStatusBadge($drug['worst_status'])?></div>
<div class='card-body'><p class='text-muted'><b>Classe:</b> <?=sanitize($drug['class'])?></p>
<table class='table table-bordered'><thead class='table-light'><tr><th>Gene</th><th>SNP</th><th>Genotipo</th><th>Tipo</th><th>Efeito</th><th>Status</th></tr></thead><tbody>
<?php foreach($drug['genes'] as $g):?>
<tr class='<?=($g['status']??'')==='risk'?'table-danger':(($g['status']??'')==='attention'?'table-warning':'')?>'><td><b><?=$g['gene_symbol']?></b></td><td><code><?=$g['rsid']??'-'?></code></td><td><b><?=$g['patient_genotype']??'N/D'?></b></td><td><span class='badge bg-secondary'><?=$g['interaction_type']?></span></td><td><?=sanitize($g['effect_description']??'')?></td><td><?=genomicStatusBadge($g['status']??'unknown')?></td></tr>
<?php endforeach;?></tbody></table></div></div>
<?php endforeach;?>
<?php if(empty($drugAnalysis)):?><div class='alert alert-warning'>Nenhuma interacao encontrada.</div><?php endif;?>
<style>@media print{.sidebar,.page-header>div:last-child,.card-body form,nav,.alert-info{display:none!important}.card{break-inside:avoid}body{font-size:11pt}}</style>
<?php require_once __DIR__.'/../../includes/footer.php';?>
