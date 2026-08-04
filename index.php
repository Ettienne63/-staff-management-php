<?php

require_once __DIR__ . '/config/database.php';

$page = $_GET['page'] ?? 'login';
$error = '';

if ($page === 'dashboard') {
    require_once __DIR__ . '/app/views/dashboard.php';
    exit;
}
if ($page === 'staff') {
    require_once __DIR__ . '/app/models/staff.php';

    $staffModel = new Staff($connection);
    $staffMembers = $staffModel->getAll();

    require_once __DIR__ . '/app/views/staffIndex.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/app/controllers/authController.php';

    $controller = new AuthController($connection);
    $error = $controller->login();
}

require_once __DIR__ . '/app/views/auth/login.php';