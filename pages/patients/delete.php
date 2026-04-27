<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) {
    setFlash('danger', 'Paciente não encontrado.');
    redirect(baseUrl('pages/patients/list.php'));
}

// Excluir arquivos de exames do paciente
$files = $pdo->prepare("
    SELECT ef.file_path FROM exam_files ef 
    JOIN exams e ON ef.exam_id = e.id 
    WHERE e.patient_id = ?
");
$files->execute([$id]);
foreach ($files->fetchAll() as $f) {
    $filePath = __DIR__ . '/../../' . $f['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

// Excluir arquivos de prontuários
$recFiles = $pdo->prepare("
    SELECT rf.file_path FROM record_files rf 
    JOIN medical_records mr ON rf.record_id = mr.id 
    WHERE mr.patient_id = ?
");
$recFiles->execute([$id]);
foreach ($recFiles->fetchAll() as $f) {
    $filePath = __DIR__ . '/../../' . $f['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

// Excluir registros (CASCADE cuidará dos filhos, mas por segurança)
$pdo->prepare("DELETE FROM exam_files WHERE exam_id IN (SELECT id FROM exams WHERE patient_id = ?)")->execute([$id]);
$pdo->prepare("DELETE FROM exams WHERE patient_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM record_files WHERE record_id IN (SELECT id FROM medical_records WHERE patient_id = ?)")->execute([$id]);
$pdo->prepare("DELETE FROM medical_records WHERE patient_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM patients WHERE id = ?")->execute([$id]);

setFlash('success', 'Paciente "' . $patient['name'] . '" excluído com sucesso.');
redirect(baseUrl('pages/patients/list.php'));