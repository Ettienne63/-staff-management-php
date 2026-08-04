<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/user.php';

class AuthController
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return 'Email and password are required.';
        }

        $userModel = new User($this->connection);
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return 'Invalid email or password.';
        }

        header('Location: index.php?page=dashboard');
        exit;
    }
}