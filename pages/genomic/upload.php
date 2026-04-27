<?php
$pageTitle='Upload Genomico';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/genomic.php';
requireLogin();
$patientId=intval($_GET['patient_id']??0);
if(!$patientId||!canAccessPatient($patientId)){setFlash('danger','Paciente nao encontrado');redirect(baseUrl('pages/patients/list.php'));}
$pdo=getConnection();$patient=$pdo->prepare("SELECT * FROM patients WHERE id=?");$patient->execute([$patientId]);$patient=$patient->fetch();
if(!$patient){redirect(baseUrl('pages/patients/list.php'));}
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_FILES['genomic_file'])){
    $file=$_FILES['genomic_file'];
    if($file['error']!==UPLOAD_ERR_OK){setFlash('danger','Erro upload');}
    elseif($file['size']>100*1024*1024){setFlash('danger','Max 100MB');}
    else{$result=importGenomicCSV($patientId,$file['tmp_name'],$file['name'],getCurrentUserId());
        if($result['success']){setFlash('success',number_format($result['imported'],0,',','.').' SNPs importados. Analise executada.');redirect(baseUrl('pages/genomic/dashboard.php?patient_id='.$patientId));}
        else{setFlash('danger','Erro: '.$result['error']);}}}
$imports=$pdo->prepare("SELECT * FROM genomic_imports WHERE patient_id=? ORDER BY imported_at DESC");$imports->execute([$patientId]);$imports=$imports->fetchAll();
require_once __DIR__.'/../../includes/header.php';?>
<div class='page-header'><h1><i class='bi bi-dna me-2'></i>Upload Genomico</h1>
<a href='<?=baseUrl("pages/genomic/index.php")?>' class='btn btn-outline-secondary'><i class='bi bi-arrow-left me-1'></i>Voltar</a></div>
<div class='row'><div class='col-lg-8'>
<div class='card mb-4'><div class='card-header'><i class='bi bi-upload me-2'></i>Upload - <?=sanitize($patient['name'])?></div>
<div class='card-body'>
<div class='alert alert-info'><strong>Formatos:</strong> Genera, 23andMe, AncestryDNA (CSV/TXT). Todos os SNPs serao armazenados e analisados automaticamente.</div>
<form method='POST' enctype='multipart/form-data' id='uploadForm'>
<div class='mb-3'><label class='form-label fw-semibold'>Arquivo CSV genomico:</label>
<input type='file' name='genomic_file' class='form-control form-control-lg' accept='.csv,.txt,.tsv' required id='fileInput'></div>
<div id='fileInfo' class='mb-3 d-none'><div class='alert alert-light'><i class='bi bi-file-earmark-text me-1'></i><span id='fileName'></span> (<span id='fileSize'></span>)</div></div>
<button type='submit' class='btn btn-primary btn-lg w-100' id='submitBtn'><i class='bi bi-cloud-upload me-2'></i>Importar e Analisar</button>
</form>
<div id='progressArea' class='mt-3 d-none'>
<h6 class='mb-2'><i class='bi bi-hourglass-split me-1'></i>Processando...</h6>
<div class='progress mb-2' style='height:30px;'><div class='progress-bar progress-bar-striped progress-bar-animated bg-primary' id='progressBar' style='width:0%'><span id='progressText'>0%</span></div></div>
<div id='statusText' class='text-muted small'>Enviando arquivo...</div>
<div class='alert alert-warning mt-2 small'><i class='bi bi-exclamation-triangle me-1'></i>Nao feche esta pagina. Arquivos grandes (~600K SNPs) podem levar 1-3 minutos.</div>
</div></div></div></div>
<div class='col-lg-4'>
<div class='card'><div class='card-header'><i class='bi bi-clock-history me-2'></i>Importacoes</div>
<div class='card-body p-0'><?php if(empty($imports)):?><div class='text-center py-4 text-muted'><i class='bi bi-inbox' style='font-size:2em;'></i><p class='mt-2'>Nenhuma importacao</p></div>
<?php else:?><div class='list-group list-group-flush'><?php foreach($imports as $imp):?>
<div class='list-group-item'><div class='d-flex justify-content-between'><strong class='text-truncate'><?=sanitize($imp['file_name'])?></strong>
<span class='badge <?=$imp['status']==='completed'?'bg-success':($imp['status']==='error'?'bg-danger':'bg-warning')?>'><?=$imp['status']?></span></div>
<small class='text-muted'><?=formatDateTime($imp['imported_at'])?> | <?=number_format($imp['imported_snps']??0,0,',','.')?> SNPs</small></div>
<?php endforeach;?></div><?php endif;?></div></div>
<?php if(hasGenomicData($patientId)):?><div class='card mt-3'><div class='card-body text-center'>
<a href='<?=baseUrl("pages/genomic/dashboard.php?patient_id=".$patientId)?>' class='btn btn-success btn-lg w-100'><i class='bi bi-bar-chart me-2'></i>Ver Dashboard</a></div></div><?php endif;?>
</div></div>
<script>
document.getElementById('fileInput').addEventListener('change',function(e){
    var f=e.target.files[0];if(f){document.getElementById('fileName').textContent=f.name;
    var s=f.size;var u='bytes';if(s>1048576){s=(s/1048576).toFixed(1);u='MB';}else if(s>1024){s=(s/1024).toFixed(0);u='KB';}
    document.getElementById('fileSize').textContent=s+' '+u;document.getElementById('fileInfo').classList.remove('d-none');}});
document.getElementById('uploadForm').addEventListener('submit',function(e){
    e.preventDefault();var form=this;var fd=new FormData(form);var xhr=new XMLHttpRequest();
    document.getElementById('submitBtn').disabled=true;document.getElementById('submitBtn').innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
    document.getElementById('progressArea').classList.remove('d-none');
    xhr.upload.addEventListener('progress',function(e){if(e.lengthComputable){var pct=Math.round(e.loaded/e.total*100);
        document.getElementById('progressBar').style.width=pct+'%';document.getElementById('progressText').textContent=pct+'%';
        if(pct<100){document.getElementById('statusText').textContent='Enviando: '+pct+'% ('+Math.round(e.loaded/1048576)+' MB de '+Math.round(e.total/1048576)+' MB)';}
        else{document.getElementById('statusText').textContent='Upload completo. Processando SNPs e executando analise... Aguarde.';
            document.getElementById('progressBar').classList.remove('bg-primary');document.getElementById('progressBar').classList.add('bg-warning');}}});
    xhr.addEventListener('load',function(){document.getElementById('progressBar').style.width='100%';document.getElementById('progressBar').classList.remove('bg-warning');document.getElementById('progressBar').classList.add('bg-success');document.getElementById('progressText').textContent='Concluido!';
        setTimeout(function(){form.submit();},500);});
    xhr.addEventListener('error',function(){document.getElementById('statusText').textContent='Erro no envio. Tente novamente.';document.getElementById('submitBtn').disabled=false;});
    xhr.open('POST',window.location.href);xhr.send(fd);});
</script>
<?php require_once __DIR__.'/../../includes/footer.php';?>
