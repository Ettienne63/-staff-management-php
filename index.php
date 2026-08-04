<?php

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/app/controllers/AuthController.php';

    $controller = new AuthController($connection);
    $error = $controller->login();
}

require_once __DIR__ . '/app/views/auth/login.php';