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

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'] ?? 'admin';
        $_SESSION['logged_in'] = true;

        $baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        header('Location: ' . $baseUrl . '/admin/dashboard');
        exit;
    }
}
