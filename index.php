<?php
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('pages/dashboard.php');
} else {
    redirect('pages/login.php');
}