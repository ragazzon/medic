<?php
$pageTitle='Genomica';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
$pdo=getConnection();
if(isAdmin()){
    $patients=$pdo->query("SELECT * FROM patients ORDER BY name")->fetchAll();
}else{
    $ids=getAllowedPatientIds();
    if(!empty($ids)){
        $ph=implode(',',array_fill(0,count($ids),'?'));
        $s=$pdo->prepare("SELECT * FROM patients WHERE id IN ($ph) ORDER BY name");
        $s->execute($ids);$patients=$s->fetchAll();
    }else{$patients=[];}
}
require_once __DIR__.'/../../includes/header.php';
?>
<div class='page-header'><h1><i class='bi bi-dna me-2'></i>Genomica</h1></div>
<div class='row g-3'>
<?php foreach($patients as $p):?>
<div class='col-md-6 col-lg-4'>
<div class='card h-100'>
<div class='card-body'>
<h5><?=sanitize($p['name'])?></h5>
<p class='text-muted mb-2'><?=$p['birth_date']?date('d/m/Y',strtotime($p['birth_date'])):''?></p>
<?php if(hasGenomicData($p['id'])):?>
<a href='<?=baseUrl("pages/genomic/dashboard.php?patient_id=".$p["id"])?>' class='btn btn-primary btn-sm me-1'><i class='bi bi-bar-chart me-1'></i>Dashboard</a>
<a href='<?=baseUrl("pages/genomic/argue.php?patient_id=".$p["id"])?>' class='btn btn-outline-danger btn-sm'><i class='bi bi-chat-left-quote me-1'></i>Argumente</a>
<?php else:?>
<a href='<?=baseUrl("pages/genomic/upload.php?patient_id=".$p["id"])?>' class='btn btn-outline-primary btn-sm'><i class='bi bi-upload me-1'></i>Upload CSV</a>
<span class='badge bg-secondary ms-2'>Sem dados</span>
<?php endif;?>
</div></div></div>
<?php endforeach;?>
<?php if(empty($patients)):?>
<div class='col-12'><div class='alert alert-info'>Nenhum paciente cadastrado.</div></div>
<?php endif;?>
</div>
<?php require_once __DIR__.'/../../includes/footer.php';?>
