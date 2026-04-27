<?php
$pageTitle = 'Upload Genômico';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/genomic.php';
requireLogin();

$patientId = intval($_GET['patient_id'] ?? 0);
if (!$patientId || !canAccessPatient($patientId)) {
    setFlash('danger', 'Paciente não encontrado');
    redirect(baseUrl('pages/patients/list.php'));
}

$pdo = getConnection();
$patient = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$patient->execute([$patientId]);
$patient = $patient->fetch();
if (!$patient) { redirect(baseUrl('pages/patients/list.php')); }

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['genomic_file'])) {
    $file = $_FILES['genomic_file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Erro no upload do arquivo');
    } elseif ($file['size'] > 100 * 1024 * 1024) { // 100MB limit for genomic files
        setFlash('danger', 'Arquivo muito grande (máx 100MB)');
    } else {
        // Save to temp and process
        $tmpPath = $file['tmp_name'];
        $result = importGenomicCSV($patientId, $tmpPath, $file['name'], getCurrentUserId());
        
        if ($result['success']) {
            setFlash('success', sprintf('Importação concluída! %s SNPs importados de %s total. Análise executada automaticamente.',
                number_format($result['imported'], 0, ',', '.'),
                number_format($result['total'], 0, ',', '.')
            ));
            redirect(baseUrl('pages/genomic/dashboard.php?patient_id=' . $patientId));
        } else {
            setFlash('danger', 'Erro na importação: ' . $result['error']);
        }
    }
}

// Check existing imports
$imports = $pdo->prepare("SELECT * FROM genomic_imports WHERE patient_id = ? ORDER BY imported_at DESC");
$imports->execute([$patientId]);
$imports = $imports->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-dna me-2"></i>Upload Genômico</h1>
    <a href="<?= baseUrl('pages/patients/view.php?id=' . $patientId) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-upload me-2"></i>Upload CSV - <?= sanitize($patient['name']) ?>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle me-1"></i>Formatos aceitos:</h6>
                    <ul class="mb-0">
                        <li><strong>Genera</strong> - Dados brutos CSV</li>
                        <li><strong>23andMe</strong> - Raw data download</li>
                        <li><strong>AncestryDNA</strong> - Raw DNA data</li>
                    </ul>
                    <small class="text-muted">O arquivo é processado localmente e todos os SNPs são armazenados.</small>
                </div>
                
                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Arquivo CSV genômico:</label>
                        <input type="file" name="genomic_file" class="form-control" accept=".csv,.txt,.tsv" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                        <i class="bi bi-cloud-upload me-2"></i>Import