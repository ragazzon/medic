<?php
require_once __DIR__ . '/functions.php';
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$parentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' - ' : '' ?>MEDIC</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= baseUrl('assets/css/style.css') ?>" rel="stylesheet">
    <!-- Mammoth.js for DOCX preview -->
    <script src="https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js"></script>
</head>
<body>
<?php if (isLoggedIn()): ?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?= baseUrl('pages/dashboard.php') ?>" class="sidebar-brand">
            <i class="bi bi-heart-pulse"></i>
            <span>MEDIC</span>
        </a>
        <button class="btn btn-link sidebar-toggle d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= baseUrl('pages/dashboard.php') ?>">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'patients' || $currentPage === 'patients' ? 'active' : '' ?>" href="<?= baseUrl('pages/patients/list.php') ?>">
                    <i class="bi bi-people"></i>
                    <span>Pacientes</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-divider"></div>
        <small class="text-muted px-3 mb-1 d-block" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;">Registros Clínicos</small>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'records' ? 'active' : '' ?>" href="<?= baseUrl('pages/records/list.php') ?>">
                    <i class="bi bi-journal-medical"></i>
                    <span>Consultas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'exams' ? 'active' : '' ?>" href="<?= baseUrl('pages/exams/list.php') ?>">
                    <i class="bi bi-clipboard2-pulse"></i>
                    <span>Exames</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'medications' ? 'active' : '' ?>" href="<?= baseUrl('pages/medications/list.php') ?>">
                    <i class="bi bi-capsule"></i>
                    <span>Medicamentos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'genomic' ? 'active' : '' ?>" href="<?= baseUrl('pages/genomic/index.php') ?>" title="Análise genética">
                    <i class="bi bi-dna"></i>
                    <span>Genética</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'timeline' ? 'active' : '' ?>" href="<?= baseUrl('pages/timeline.php') ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Linha do Tempo</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'planner' ? 'active' : '' ?>" href="<?= baseUrl('pages/planner.php') ?>">
                    <i class="bi bi-calendar2-week"></i>
                    <span>Agenda</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-divider"></div>
        <small class="text-muted px-3 mb-1 d-block" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;">Cadastros</small>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'specialties' ? 'active' : '' ?>" href="<?= baseUrl('pages/specialties/list.php') ?>">
                    <i class="bi bi-heart-pulse"></i>
                    <span>Especialidades</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-divider"></div>
        <small class="text-muted px-3 mb-1 d-block" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;">Análises</small>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'reports' ? 'active' : '' ?>" href="<?= baseUrl('pages/reports/index.php') ?>">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Relatórios</span>
                </a>
            </li>
        </ul>
        
        <?php if (isAdmin()): ?>
        <div class="sidebar-divider"></div>
        <small class="text-muted px-3 mb-1 d-block" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;">Administração</small>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $parentDir === 'users' ? 'active' : '' ?>" href="<?= baseUrl('pages/users/list.php') ?>">
                    <i class="bi bi-person-gear"></i>
                    <span>Gerenciar Usuários</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($parentDir === 'admin' && $currentPage === 'access_logs') ? 'active' : '' ?>" href="<?= baseUrl('pages/admin/access_logs.php') ?>">
                    <i class="bi bi-shield-lock"></i>
                    <span>Logs de Acesso</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($parentDir === 'admin' && $currentPage === 'local_mode') ? 'active' : '' ?>" href="<?= baseUrl('pages/admin/local_mode.php') ?>">
                    <?php if (defined('LOCAL_MODE') && LOCAL_MODE): ?>
                        <i class="bi bi-pc-display"></i>
                        <span>Modo Local</span>
                    <?php else: ?>
                        <i class="bi bi-download"></i>
                        <span>Uso Local</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if (defined('LOCAL_MODE') && LOCAL_MODE): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($parentDir === 'admin' && $currentPage === 'local_sync') ? 'active' : '' ?>" href="<?= baseUrl('pages/admin/local_sync.php') ?>">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span>Sincronizar</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <?php endif; ?>
        
        <div class="sidebar-divider"></div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>" href="<?= baseUrl('pages/profile.php') ?>">
                    <i class="bi bi-person-circle"></i>
                    <span>Meu Perfil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= baseUrl('pages/logout.php') ?>">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Sair</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <i class="bi bi-person-circle"></i>
            <div>
                <small class="d-block fw-semibold"><?= sanitize($currentUser['name'] ?? '') ?></small>
                <small class="text-muted"><?= ($currentUser['role'] ?? '') === 'admin' ? 'Administrador' : 'Usuário' ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Conteúdo principal -->
<div class="main-content" id="mainContent">
    <!-- Topbar -->
    <nav class="topbar">
        <button class="btn btn-link sidebar-toggle d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-right">
            <?php if (defined('LOCAL_MODE') && LOCAL_MODE): ?>
            <span class="badge bg-warning text-dark me-3">
                <i class="bi bi-pc-display me-1"></i>Modo Local
            </span>
            <?php endif; ?>
            <span class="d-none d-md-inline text-muted me-3">
                <i class="bi bi-calendar3"></i> <?= date('d/m/Y') ?>
            </span>
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-md-inline"><?= sanitize($currentUser['name'] ?? '') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= baseUrl('pages/profile.php') ?>"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                    <?php if (isAdmin()): ?>
                    <li><a class="dropdown-item" href="<?= baseUrl('pages/users/list.php') ?>"><i class="bi bi-person-gear me-2"></i>Gerenciar Usuários</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= baseUrl('pages/logout.php') ?>"><i class="bi bi-box-arrow-left me-2"></i>Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Container do conteúdo -->
    <div class="content-wrapper">
        <?php
        $flash = getFlash();
        if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
<?php else: ?>
<!-- Layout para páginas sem autenticação (login/registro) -->
<div class="auth-wrapper">
    <?php
    $flash = getFlash();
    if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
        <?= $flash['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
<?php endif; ?>