<?php
$pageTitle='Dashboard Genomico'; require_once __DIR__.'/../../includes/auth.php'; require_once __DIR__.'/../../includes/genomic.php'; requireLogin();
$patientId=intval($_GET['patient_id']??0); if(!$patientId||!canAccessPatient($patientId)){redirect(baseUrl('pages/patients/list.php'));}
$pdo=getConnection(); $p=$pdo->prepare('SELECT * FROM patients WHERE id=?'); $p->execute([$patientId]); $patient=$p->fetch();
$summary=getGenomicSummary($patientId); if(!$summary){setFlash('info','Sem dados.');redirect(baseUrl('pages/genomic/upload.php?patient_id='.$patientId));}
$drugs=getDrugAnalysis($patientId); require_once __DIR__.'/../../includes/header.php'; ?>
<div class='page-header'><h1><i class='bi bi-bar-chart me-2'></i>Genomica - <?=sanitize($patient['name'])?></h1>
<div><a href='<?=baseUrl('pages/genomic/argue.php?patient_id='.$patientId)?>' class='btn btn-outline-danger btn-sm'><i class='bi bi-chat-left-quote me-1'></i>Argumente com Medico</a>
<a href='<?=baseUrl('pages/genomic/upload.php?patient_id='.$patientId)?>' class='btn btn-outline-secondary btn-sm'><i class='bi bi-upload me-1'></i>Re-importar</a>
<a href='<?=baseUrl('pages/patients/view.php?id='.$patientId)?>' class='btn btn-outline-secondary btn-sm'><i class='bi bi-arrow-left me-1'></i>Voltar</a></div></div>
<?php if($summary['import']):?><div class='alert alert-light border mb-4'><i class='bi bi-info-circle me-1'></i><strong><?=sanitize($summary['import']['file_name'])?></strong> | <?=number_format($summary['import']['imported_snps'],0,',','.')?> SNPs | <?=$summary['import']['genome_build']??''?> | <?=formatDateTime($summary['import']['imported_at'])?></div><?php endif;?>
<?php if(!empty($drugs)):?><h5 class='mb-3'><i class='bi bi-capsule me-2'></i>Medicamentos</h5><div class='row g-3 mb-4'>
<?php foreach($drugs as $d):?><div class='col-md-4 col-lg-3'><div class='card h-100 border-start border-4 <?=$d['worst_status']==='risk'?'border-danger':($d['worst_status']==='attention'?'border-warning':'border-success')?>'>
<div class='card-body'><div class='d-flex justify-content-between mb-2'><h6 class='mb-0'><?=sanitize($d['name'])?></h6><?=genomicStatusBadge($d['worst_status'])?></div>
<small class='text-muted'><?=sanitize($d['class'])?></small><div class='mt-2'><?php foreach($d['genes'] as $g):?>
<div class='d-flex justify-content-between py-1 border-top'><small><b><?=$g['gene_symbol']?></b></small><small><?=genomicStatusIcon($g['status']??'unknown')?> <?=$g['patient_genotype']??'N/D'?></small></div>
<?php endforeach;?></div></div></div></div><?php endforeach;?></div><?php endif;?>
<h5 class='mb-3'><i class='bi bi-grid me-2'></i>Paineis</h5><div class='row g-3 mb-4'>
<?php foreach($summary['panels'] as $pnl):?><div class='col-md-6 col-lg-4'><a href='<?=baseUrl('pages/genomic/panel.php?patient_id='.$patientId.'&panel='.$pnl['code'])?>' class='text-decoration-none'>
<div class='card h-100'><div class='card-body'><div class='d-flex align-items-center mb-2'>
<div class='rounded-circle d-flex align-items-center justify-content-center me-2' style='width:40px;height:40px;background:<?=$pnl['color']?>20;'><i class='bi <?=$pnl['icon']?>' style='color:<?=$pnl['color']?>;'></i></div>
<h6 class='mb-0'><?=sanitize($pnl['name'])?></h6></div>
<div class='d-flex gap-2'><?php if($pnl['risk_count']):?><span class='badge bg-danger'><?=$pnl['risk_count']?> risco</span><?php endif;?>
<?php if($pnl['attention_count']):?><span class='badge bg-warning text-dark'><?=$pnl['attention_count']?> atencao</span><?php endif;?>
<span class='badge bg-success'><?=$pnl['normal_count']?> ok</span></div></div></div></a></div>
<?php endforeach;?></div><?php require_once __DIR__.'/../../includes/footer.php';?>
