<?php

require_once __DIR__ . '/config/database.php';

$page = $_GET['page'] ?? 'login';
$error = '';

if ($page === 'dashboard') {
    require_once __DIR__ . '/app/views/dashboard.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/app/controllers/authController.php';

    $controller = new AuthController($connection);
    $error = $controller->login();
}

require_once __DIR__ . '/app/views/auth/login.php';