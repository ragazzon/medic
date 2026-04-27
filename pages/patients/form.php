<?php
$pageTitle = 'Paciente';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$patient = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    $patient = $stmt->fetch();
    if (!$patient) {
        setFlash('danger', 'Paciente não encontrado.');
        redirect(baseUrl('pages/patients/list.php'));
    }
    $pageTitle = 'Editar Paciente';
} else {
    $pageTitle = 'Novo Paciente';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'birth_date' => dateToDb($_POST['birth_date'] ?? ''),
        'gender' => $_POST['gender'] ?? '',
        'cpf' => trim($_POST['cpf'] ?? ''),
        'blood_type' => $_POST['blood_type'] ?? '',
        'relationship' => trim($_POST['relationship'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'allergies' => trim($_POST['allergies'] ?? ''),
        'chronic_conditions' => trim($_POST['chronic_conditions'] ?? ''),
        'medications' => trim($_POST['medications'] ?? ''),
        'health_insurance' => trim($_POST['health_insurance'] ?? ''),
        'insurance_number' => trim($_POST['insurance_number'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    if (empty($data['name']) || empty($data['birth_date'])) {
        setFlash('danger', 'Nome e data de nascimento são obrigatórios.');
    } else {
        // Handle photo upload
        $photoPath = null;
        $photoChanged = false;

        // Remove photo if checkbox checked
        if (!empty($_POST['remove_photo']) && $id && !empty($patient['photo'])) {
            deleteFile($patient['photo']);
            $photoPath = '';
            $photoChanged = true;
        }

        // Upload new photo if provided
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowedPhotoTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $photoMime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($photoMime, $allowedPhotoTypes)) {
                setFlash('danger', 'Tipo de imagem não permitido. Use JPG, PNG, GIF ou WebP.');
                redirect(baseUrl('pages/patients/form.php?id=' . $id));
            }
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                setFlash('danger', 'A foto deve ter no máximo 5MB.');
                redirect(baseUrl('pages/patients/form.php?id=' . $id));
            }

            $uploadDir = __DIR__ . '/../../uploads/patients/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $newName = 'photo_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newName)) {
                // Delete old photo if exists
                if ($id && !empty($patient['photo'])) {
                    deleteFile($patient['photo']);
                }
                $photoPath = 'uploads/patients/' . $newName;
                $photoChanged = true;
            }
        }

        if ($id) {
            $fields = [];
            $values = [];
            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
            if ($photoChanged) {
                $fields[] = "photo = ?";
                $values[] = $photoPath;
            }
            $values[] = $id;
            $stmt = $pdo->prepare("UPDATE patients SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($values);
            setFlash('success', 'Paciente atualizado com sucesso!');
        } else {
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            $fields[] = 'created_by';
            $placeholders[] = '?';
            $values = array_values($data);
            $values[] = getCurrentUserId();
            if ($photoChanged && $photoPath) {
                $fields[] = 'photo';
                $placeholders[] = '?';
                $values[] = $photoPath;
            }
            $stmt = $pdo->prepare("INSERT INTO patients (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")");
            $stmt->execute($values);
            $id = $pdo->lastInsertId();
            setFlash('success', 'Paciente cadastrado com sucesso!');
        }
        redirect(baseUrl('pages/patients/view.php?id=' . $id));
    }
}

$d = $patient ?? $_POST ?? [];

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-person-plus me-2"></i><?= $id ? 'Editar' : 'Novo' ?> Paciente</h1>
    <a href="<?= baseUrl('pages/patients/list.php') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Foto do Paciente -->
            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-camera me-2"></i>Foto do Paciente</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="d-flex align-items-center gap-4">
                        <?php if (!empty($patient['photo']) && file_exists(__DIR__ . '/../../' . $patient['photo'])): ?>
                        <div class="text-center">
                            <img src="<?= baseUrl($patient['photo']) ?>" alt="Foto" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;border:3px solid var(--primary-light);">
                            <div class="mt-2">
                                <label class="form-check">
                                    <input type="checkbox" name="remove_photo" value="1" class="form-check-input">
                                    <small class="text-danger">Remover foto</small>
                                </label>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="text-center">
                            <div style="width:100px;height:100px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:40px;">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <label class="form-label">Selecionar foto</label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="text-muted">Formatos: JPG, PNG, GIF, WebP. Máx. 5MB.</small>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-person me-2"></i>Dados Pessoais</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nome completo *</label>
                    <input type="text" name="name" class="form-control" value="<?= sanitize($d['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data de nascimento *</label>
                    <input type="text" name="birth_date" class="form-control date-br" value="<?= dateToForm($d['birth_date'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gênero</label>
                    <select name="gender" class="form-select">
                        <option value="">Selecione</option>
                        <option value="M" <?= ($d['gender'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= ($d['gender'] ?? '') === 'F' ? 'selected' : '' ?>>Feminino</option>
                        <option value="O" <?= ($d['gender'] ?? '') === 'O' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">CPF</label>
                    <input type="text" name="cpf" class="form-control mask-cpf" value="<?= sanitize($d['cpf'] ?? '') ?>" placeholder="000.000.000-00">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo Sanguíneo</label>
                    <select name="blood_type" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                        <option value="<?= $bt ?>" <?= ($d['blood_type'] ?? '') === $bt ? 'selected' : '' ?>><?= $bt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Parentesco</label>
                    <input type="text" name="relationship" class="form-control" value="<?= sanitize($d['relationship'] ?? '') ?>" placeholder="Ex: Pai, Mãe, Filho">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="phone" class="form-control mask-phone" value="<?= sanitize($d['phone'] ?? '') ?>" placeholder="(00) 00000-0000">
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?= sanitize($d['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Endereço</label>
                    <input type="text" name="address" class="form-control" value="<?= sanitize($d['address'] ?? '') ?>">
                </div>
            </div>

            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-heart-pulse me-2"></i>Informações Médicas</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Alergias</label>
                    <textarea name="allergies" class="form-control" rows="3" placeholder="Liste as alergias conhecidas..."><?= sanitize($d['allergies'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Condições crônicas</label>
                    <textarea name="chronic_conditions" class="form-control" rows="3" placeholder="Diabetes, hipertensão, etc..."><?= sanitize($d['chronic_conditions'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Medicamentos em uso</label>
                    <textarea name="medications" class="form-control" rows="3" placeholder="Medicamentos de uso contínuo..."><?= sanitize($d['medications'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Observações gerais..."><?= sanitize($d['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <h5 class="mb-3 fw-semibold text-primary"><i class="bi bi-shield-check me-2"></i>Plano de Saúde</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Convênio</label>
                    <input type="text" name="health_insurance" class="form-control" value="<?= sanitize($d['health_insurance'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número da carteirinha</label>
                    <input type="text" name="insurance_number" class="form-control" value="<?= sanitize($d['insurance_number'] ?? '') ?>">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= $id ? 'Atualizar' : 'Cadastrar' ?>
                </button>
                <a href="<?= baseUrl('pages/patients/list.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>