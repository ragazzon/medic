<?php
$pageTitle = 'Cadastro';
require_once __DIR__ . '/../includes/auth.php';

// Registro público desabilitado - apenas admin pode criar usuários
setFlash('warning', 'O registro público está desabilitado. Solicite acesso ao administrador.');
redirect(baseUrl('pages/login.php'));