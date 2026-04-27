<?php
/**
 * MEDIC - Export endpoint (fora da pasta admin para evitar bloqueio do InfinityFree)
 */
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require __DIR__ . '/admin/local_export_logic.php';