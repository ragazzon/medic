<?php
$pageTitle = 'Medicamento';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$patientIdDefault = intval($_GET['patient_id'] ?? 0);
$medication = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM medications WHERE id = ?");
    $stmt->execute([$id]);
    $medication = $stmt->fetch();
    if (!$medication) {
        setFlash('danger', 'Medicamento não encontrado.');
        redirect(baseUrl('pages/medications/list.php'));
    }
    $pageTitle = 'Editar Medicamento';
    $patientIdDefault = $medication['patient_id'];
} else {
    $pageTitle = 'Novo Medicamento';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'patient_id' => intval($_POST['patient_id'] ?? 0),
        'name' => trim($_POST['name'] ?? ''),
        'active_ingredient' => trim($_POST['active_ingredient'] ?? ''),
        'dosage' => trim($_POST['dosage'] ?? ''),
        'frequency' => trim($_POST['frequency'] ?? ''),
        'route' => trim($_POST['route'] ?? ''),
        'start_date' => dateToDb($_POST['start_date'] ?? ''),
        'end_date' => dateToDb($_POST['end_date'] ?? ''),
        'prescriber' => trim($_POST['prescriber'] ?? ''),
        'specialty' => trim($_POST['specialty'] ?? ''),
        'reason' => trim($_POST['reason'] ?? ''),
        'instructions' => trim($_POST['instructions'] ?? ''),
        'side_effects' => trim($_POST['side_effects'] ?? ''),
        'is_continuous' => isset($_POST['is_continuous']) ? 1 : 0,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    // Limpar end_date vazia
    if (empty($data['end_date'])) $data['end_date'] = null;
    if (empty($data['start_date'])) $data['start_date'] = null;

    if (empty($data['patient_id']) || empty($data['name'])) {
        setFlash('danger', 'Paciente e nome do medicamento são obrigatórios.');
    } else {
        if ($id) {
            $fields = [];
            $values = [];
            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
            $values[] = $id;
            $pdo->prepare("UPDATE medications SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
            setFlash('success', 'Medicamento atualizado!');
        } else {
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            $fields[] = 'created_by';
            $placeholders[] = '?';
            $values = array_values($data);
            $values[] = getCurrentUserId();
            $pdo->prepare("INSERT INTO medications (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")")->execute($values);
            $id = $pdo->lastInsertId();
            setFlash('success', 'Medicamento cadastrado!');
        }
        redirect(baseUrl('pages/medications/list.php' . ($patientIdDefault ? '?patient_id=' . $data['patient_id'] : '')));
    }
}

$d = $medication ?? $_POST ?? [];
$patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-capsule me-2"></i><?= $id ? 'Editar' : 'Novo' ?> Medicamento</h1>
    <a href="<?= baseUrl('pages/medications/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-capsule me-2"></i>Dados do Medicamento</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($d['patient_id'] ?? $patientIdDefault) == $p['id'] ? 'selected' : '' ?>><?= sanitize($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nome do medicamento *</label>
                    <input type="text" name="name" class="form-control" value="<?= sanitize($d['name'] ?? '') ?>" placeholder="Ex: Losartana, Metformina..." required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Princípio ativo</label>
                    <input type="text" name="active_ingredient" class="form-control" value="<?= sanitize($d['active_ingredient'] ?? '') ?>" placeholder="Ex: Losartana potássica">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dosagem</label>
                    <input type="text" name="dosage" class="form-control" value="<?= sanitize($d['dosage'] ?? '') ?>" placeholder="Ex: 50mg, 500mg">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Frequência</label>
                    <input type="text" name="frequency" class="form-control" value="<?= sanitize($d['frequency'] ?? '') ?>" placeholder="Ex: 1x/dia, 8/8h, 12/12h">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Via de administração</label>
                    <select name="route" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach (['Oral','Sublingual','Intravenosa','Intramuscular','Subcutânea','Tópica','Inalatória','Retal','Oftálmica','Nasal','Transdérmica','Outra'] as $r): ?>
                        <option value="<?= $r ?>" <?= ($d['route'] ?? '') === $r ? 'selected' : '' ?>><?= $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data de início</label>
                    <input type="text" name="start_date" class="form-control date-br" value="<?= dateToForm($d['start_date'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data de término</label>
                    <input type="text" name="end_date" class="form-control date-br" value="<?= dateToForm($d['end_date'] ?? '') ?>" placeholder="Vazio = sem prazo">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_continuous" id="is_continuous" value="1" <?= ($d['is_continuous'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_continuous">Uso contínuo</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($d['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Em uso</label>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-person-badge me-2"></i>Prescrição</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Médico prescritor</label>
                    <input type="text" name="prescriber" class="form-control" value="<?= sanitize($d['prescriber'] ?? '') ?>" placeholder="Nome do médico">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Especialidade</label>
                    <input type="text" name="specialty" class="form-control" value="<?= sanitize($d['specialty'] ?? '') ?>" placeholder="Cardiologia, Endocrinologia...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Indicação / Motivo</label>
                    <input type="text" name="reason" class="form-control" value="<?= sanitize($d['reason'] ?? '') ?>" placeholder="Para que foi prescrito">
                </div>
            </div>

            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-info-circle me-2"></i>Informações Adicionais</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Instruções especiais</label>
                    <textarea name="instructions" class="form-control" rows="3" placeholder="Tomar em jejum, com alimento, etc."><?= sanitize($d['instructions'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Efeitos colaterais observados</label>
                    <textarea name="side_effects" class="form-control" rows="3" placeholder="Efeitos adversos relatados..."><?= sanitize($d['side_effects'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Notas adicionais..."><?= sanitize($d['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $id ? 'Atualizar' : 'Cadastrar' ?></button>
                <a href="<?= baseUrl('pages/medications/list.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>