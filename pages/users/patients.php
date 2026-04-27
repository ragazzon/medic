<?php
$pageTitle = 'Pacientes do Usuário';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('danger', 'Usuário não encontrado.');
    redirect(baseUrl('pages/users/list.php'));
}

$pageTitle = 'Pacientes de ' . $user['name'];

// Processar associações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedPatients = $_POST['patients'] ?? [];
    
    // Remover todas as associações atuais
    $pdo->prepare("DELETE FROM user_patients WHERE user_id = ?")->execute([$id]);
    
    // Inserir novas associações
    if (!empty($selectedPatients)) {
        $stmt = $pdo->prepare("INSERT INTO user_patients (user_id, patient_id) VALUES (?, ?)");
        foreach ($selectedPatients as $patientId) {
            $stmt->execute([$id, intval($patientId)]);
        }
    }
    
    setFlash('success', 'Pacientes associados atualizados com sucesso!');
    redirect(baseUrl('pages/users/patients.php?id=' . $id));
}

// Buscar todos os pacientes
$allPatients = $pdo->query("SELECT id, name, relationship, birth_date FROM patients ORDER BY name ASC")->fetchAll();

// Buscar pacientes já associados
$assignedIds = $pdo->prepare("SELECT patient_id FROM user_patients WHERE user_id = ?");
$assignedIds->execute([$id]);
$assignedIds = $assignedIds->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-people me-2"></i>Pacientes de <?= sanitize($user['name']) ?></h1>
    <a href="<?= baseUrl('pages/users/list.php') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <div>
                <i class="bi bi-person-circle" style="font-size:2.5rem;color:#6c757d;"></i>
            </div>
            <div>
                <h5 class="mb-0"><?= sanitize($user['name']) ?></h5>
                <small class="text-muted"><?= sanitize($user['email']) ?></small>
                <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?> ms-2"><?= $user['role'] === 'admin' ? 'Admin' : 'Usuário' ?></span>
            </div>
        </div>
    </div>
</div>

<?php if ($user['role'] === 'admin'): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>Administradores têm acesso a todos os pacientes automaticamente. A associação de pacientes é necessária apenas para usuários com perfil "Usuário".
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <i class="bi bi-check2-square me-2"></i>Selecione os pacientes que este usuário poderá visualizar
    </div>
    <div class="card-body">
        <?php if (empty($allPatients)): ?>
        <div class="empty-state py-4">
            <i class="bi bi-people" style="font-size:40px;"></i>
            <p class="mt-2 mb-0">Nenhum paciente cadastrado no sistema.</p>
        </div>
        <?php else: ?>
        <form method="POST" action="">
            <div class="mb-3">
                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAll(true)">
                        <i class="bi bi-check-all me-1"></i>Selecionar Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">
                        <i class="bi bi-x-lg me-1"></i>Desmarcar Todos
                    </button>
                    <span class="text-muted ms-2 align-self-center" id="selectedCount">
                        <?= count($assignedIds) ?> selecionado(s)
                    </span>
                </div>
            </div>
            
            <div class="row g-2">
                <?php foreach ($allPatients as $p): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="form-check border rounded p-3 h-100 <?= in_array($p['id'], $assignedIds) ? 'border-primary bg-light' : '' ?>">
                        <input class="form-check-input patient-checkbox" type="checkbox" 
                               name="patients[]" value="<?= $p['id'] ?>" 
                               id="patient_<?= $p['id'] ?>"
                               <?= in_array($p['id'], $assignedIds) ? 'checked' : '' ?>
                               onchange="updateCount()">
                        <label class="form-check-label w-100" for="patient_<?= $p['id'] ?>">
                            <div class="fw-semibold"><?= sanitize($p['name']) ?></div>
                            <small class="text-muted">
                                <?php if (!empty($p['relationship'])): ?>
                                <?= sanitize($p['relationship']) ?> &bull; 
                                <?php endif; ?>
                                <?php if (!empty($p['birth_date'])): ?>
                                <?= formatDate($p['birth_date']) ?>
                                <?php endif; ?>
                            </small>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Salvar Associações
                </button>
                <a href="<?= baseUrl('pages/users/list.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleAll(checked) {
    document.querySelectorAll('.patient-checkbox').forEach(cb => { cb.checked = checked; });
    updateCount();
}
function updateCount() {
    const count = document.querySelectorAll('.patient-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count + ' selecionado(s)';
    document.querySelectorAll('.form-check.border').forEach(div => {
        const cb = div.querySelector('.patient-checkbox');
        if (cb.checked) {
            div.classList.add('border-primary', 'bg-light');
        } else {
            div.classList.remove('border-primary', 'bg-light');
        }
    });
}
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>