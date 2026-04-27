<?php
$pageTitle = 'Especialidade';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$specialty = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM specialties WHERE id = ?");
    $stmt->execute([$id]);
    $specialty = $stmt->fetch();
    if (!$specialty) {
        setFlash('danger', 'Especialidade não encontrada.');
        redirect(baseUrl('pages/specialties/list.php'));
    }
    $pageTitle = 'Editar Especialidade';
} else {
    $pageTitle = 'Nova Especialidade';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        setFlash('danger', 'O nome da especialidade é obrigatório.');
    } else {
        // Verificar duplicação
        $checkStmt = $pdo->prepare("SELECT id FROM specialties WHERE LOWER(name) = LOWER(?) AND id != ?");
        $checkStmt->execute([$name, $id]);
        if ($checkStmt->fetch()) {
            setFlash('danger', 'Já existe uma especialidade com este nome.');
        } else {
            if ($id) {
                // Se renomear, atualiza também nos registros existentes
                $oldName = $specialty['name'];
                $pdo->prepare("UPDATE specialties SET name = ? WHERE id = ?")->execute([$name, $id]);
                if ($oldName !== $name) {
                    $pdo->prepare("UPDATE medical_records SET specialty = ? WHERE specialty = ?")->execute([$name, $oldName]);
                    $pdo->prepare("UPDATE exams SET specialty = ? WHERE specialty = ?")->execute([$name, $oldName]);
                    $pdo->prepare("UPDATE medications SET specialty = ? WHERE specialty = ?")->execute([$name, $oldName]);
                }
                setFlash('success', 'Especialidade atualizada!');
            } else {
                $pdo->prepare("INSERT INTO specialties (name) VALUES (?)")->execute([$name]);
                setFlash('success', 'Especialidade cadastrada!');
            }
            redirect(baseUrl('pages/specialties/list.php'));
        }
    }
}

$d = $specialty ?? $_POST ?? [];

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-heart-pulse me-2"></i><?= $id ? 'Editar' : 'Nova' ?> Especialidade</h1>
    <a href="<?= baseUrl('pages/specialties/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label">Nome da Especialidade *</label>
                    <input type="text" name="name" class="form-control" value="<?= sanitize($d['name'] ?? '') ?>" placeholder="Ex: Cardiologia, Neurologia, Ortopedia..." required autofocus>
                </div>
            </div>

            <?php if ($id): ?>
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="alert alert-info mb-0 py-2">
                        <i class="bi bi-info-circle me-1"></i>
                        <small>Ao renomear, todos os registros (consultas, exames e medicamentos) que usam esta especialidade serão atualizados automaticamente.</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $id ? 'Atualizar' : 'Cadastrar' ?></button>
                <a href="<?= baseUrl('pages/specialties/list.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>