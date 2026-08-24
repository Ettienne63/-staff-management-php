<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/user.php';

class AuthController
{
    private PDO $connection;

    public function __construct(PDO $connection)
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

        $baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        header('Location: ' . $baseUrl . '/admin/dashboard');
        exit;
    }
}
