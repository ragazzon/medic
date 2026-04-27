<?php
$pageTitle = 'Logs de Acesso';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$pdo = getConnection();

// Processar exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("DELETE FROM access_logs WHERE id = ?");
        $stmt->execute([intval($_POST['id'])]);
        setFlash('success', 'Log excluído com sucesso.');
        redirect(baseUrl('pages/admin/access_logs.php?' . http_build_query($_GET)));
    }
    if ($_POST['action'] === 'delete_all') {
        $pdo->exec("TRUNCATE TABLE access_logs");
        setFlash('success', 'Todos os logs foram excluídos.');
        redirect(baseUrl('pages/admin/access_logs.php'));
    }
    if ($_POST['action'] === 'delete_filtered') {
        // Reusa os mesmos filtros da listagem
        list($whereSQL, $params) = buildAccessLogFilters();
        $pdo->prepare("DELETE FROM access_logs " . ($whereSQL ? "WHERE " . implode(' AND ', $whereSQL) : ""))->execute($params);
        setFlash('success', 'Logs filtrados foram excluídos.');
        redirect(baseUrl('pages/admin/access_logs.php'));
    }
}

// Filtros
$filterUser = trim($_GET['user'] ?? '');
$filterAction = trim($_GET['action_type'] ?? '');
$filterDateFrom = trim($_GET['date_from'] ?? '');
$filterDateTo = trim($_GET['date_to'] ?? '');
$filterIP = trim($_GET['ip'] ?? '');

function buildAccessLogFilters() {
    $filterUser = trim($_GET['user'] ?? '');
    $filterAction = trim($_GET['action_type'] ?? '');
    $filterDateFrom = trim($_GET['date_from'] ?? '');
    $filterDateTo = trim($_GET['date_to'] ?? '');
    $filterIP = trim($_GET['ip'] ?? '');
    
    $where = [];
    $params = [];

    if ($filterUser) {
        $where[] = "(al.user_name LIKE ? OR al.user_email LIKE ?)";
        $params[] = "%{$filterUser}%";
        $params[] = "%{$filterUser}%";
    }
    if ($filterAction) {
        $where[] = "al.action = ?";
        $params[] = $filterAction;
    }
    if ($filterDateFrom) {
        $where[] = "DATE(al.created_at) >= ?";
        $params[] = $filterDateFrom;
    }
    if ($filterDateTo) {
        $where[] = "DATE(al.created_at) <= ?";
        $params[] = $filterDateTo;
    }
    if ($filterIP) {
        $where[] = "al.ip_address LIKE ?";
        $params[] = "%{$filterIP}%";
    }
    
    return [$where, $params];
}

list($where, $params) = buildAccessLogFilters();
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT al.* 
    FROM access_logs al
    {$whereSQL}
    ORDER BY al.created_at DESC
    LIMIT 500
");
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Estatísticas rápidas
$totalLogs = $pdo->query("SELECT COUNT(*) FROM access_logs")->fetchColumn();
$curdate = sqlCurdate();
$todayLogins = $pdo->query("SELECT COUNT(*) FROM access_logs WHERE action = 'login' AND DATE(created_at) = {$curdate}")->fetchColumn();
$todayFailed = $pdo->query("SELECT COUNT(*) FROM access_logs WHERE action = 'login_failed' AND DATE(created_at) = {$curdate}")->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-shield-lock me-2"></i>Logs de Acesso</h1>
    <div class="d-flex gap-2">
        <?php if (!empty($logs)): ?>
        <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir TODOS os logs?');">
            <input type="hidden" name="action" value="delete_all">
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i>Limpar Tudo
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-primary bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-primary"><?= $totalLogs ?></div>
                <small class="text-muted">Total de Registros</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-success"><?= $todayLogins ?></div>
                <small class="text-muted">Logins Hoje</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger bg-opacity-10 border-0">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-danger"><?= $todayFailed ?></div>
                <small class="text-muted">Falhas Hoje</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small">Usuário</label>
                <input type="text" name="user" class="form-control form-control-sm" value="<?= sanitize($filterUser) ?>" placeholder="Nome ou e-mail...">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Ação</label>
                <select name="action_type" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="login" <?= $filterAction === 'login' ? 'selected' : '' ?>>Login</option>
                    <option value="logout" <?= $filterAction === 'logout' ? 'selected' : '' ?>>Logout</option>
                    <option value="login_failed" <?= $filterAction === 'login_failed' ? 'selected' : '' ?>>Falha de Login</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Data Início</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= sanitize($filterDateFrom) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Data Fim</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= sanitize($filterDateTo) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small">IP</label>
                <input type="text" name="ip" class="form-control form-control-sm" value="<?= sanitize($filterIP) ?>" placeholder="Endereço IP...">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search"></i> Filtrar</button>
                <a href="<?= baseUrl('pages/admin/access_logs.php') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Logs -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($logs)): ?>
        <div class="empty-state py-5">
            <i class="bi bi-shield-lock" style="font-size:60px;"></i>
            <h5 class="mt-3">Nenhum log encontrado</h5>
            <p class="text-muted">Não há registros de acesso para os filtros selecionados.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>IP</th>
                        <th>Detalhes</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr class="<?= $log['action'] === 'login_failed' ? 'table-danger bg-opacity-25' : '' ?>">
                        <td>
                            <small><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></small>
                        </td>
                        <td>
                            <?php if ($log['user_name']): ?>
                                <span class="fw-semibold"><?= sanitize($log['user_name']) ?></span>
                                <br><small class="text-muted"><?= sanitize($log['user_email']) ?></small>
                            <?php else: ?>
                                <small class="text-muted"><?= sanitize($log['user_email'] ?? 'Desconhecido') ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $actionBadge = match($log['action']) {
                                'login' => '<span class="badge bg-success"><i class="bi bi-box-arrow-in-right me-1"></i>Login</span>',
                                'logout' => '<span class="badge bg-secondary"><i class="bi bi-box-arrow-right me-1"></i>Logout</span>',
                                'login_failed' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Falha</span>',
                                default => '<span class="badge bg-light text-dark">' . sanitize($log['action']) . '</span>',
                            };
                            echo $actionBadge;
                            ?>
                        </td>
                        <td><code class="small"><?= sanitize($log['ip_address'] ?? '-') ?></code></td>
                        <td>
                            <?php if ($log['details']): ?>
                                <small class="text-muted"><?= sanitize($log['details']) ?></small>
                            <?php else: ?>
                                <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <form method="POST" class="d-inline" onsubmit="return confirm('Excluir este log?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $log['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Exibindo <?= count($logs) ?> registro(s) (máx. 500)</small>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>