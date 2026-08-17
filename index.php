<?php

require_once __DIR__ . '/config/database.php';

$page = $_GET['page'] ?? 'login';
$error = '';

if ($page === 'dashboard') {
    require_once __DIR__ . '/app/views/dashboard.php';
    exit;
}

if ($page === 'seed-admin') {
    require_once __DIR__ . '/app/models/user.php';

    $userModel = new User($connection);
    $userModel->create('admin@example.com', 'password');

    echo 'Admin user created successfully. Login with admin@example.com / password';
    exit;
}

if ($page === 'staff') {
    require_once __DIR__ . '/app/models/staff.php';

    $staffModel = new Staff($connection);
    $staffMembers = $staffModel->getAll();

    require_once __DIR__ . '/app/views/staffIndex.php';
    exit;
}

if ($page === 'create-staff') {
    require_once __DIR__ . '/app/models/staff.php';

    $staffModel = new Staff($connection);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $staffModel->create(
            $_POST['first_name'] ?? '',
            $_POST['last_name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['department'] ?? '',
            $_POST['position'] ?? ''
        );

        header('Location: index.php?page=staff');
        exit;
    }

    require_once __DIR__ . '/app/views/createStaff.php';
    exit;
}

if ($page === 'edit-staff') {
    require_once __DIR__ . '/app/models/staff.php';

    $staffModel = new Staff($connection);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int) ($_POST['id'] ?? 0);

        $staffModel->update(
            $id,
            $_POST['first_name'] ?? '',
            $_POST['last_name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['department'] ?? '',
            $_POST['position'] ?? ''
        );

        header('Location: index.php?page=staff');
        exit;
    }

    $staffMember = $staffModel->getById((int) ($_GET['id'] ?? 0));

    require_once __DIR__ . '/app/views/editStaff.php';
    exit;
}

if ($page === 'delete-staff') {
    require_once __DIR__ . '/app/models/staff.php';

    $staffModel = new Staff($connection);
    $staffModel->delete((int) ($_GET['id'] ?? 0));

    header('Location: index.php?page=staff');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/app/controllers/authController.php';

    $controller = new AuthController($connection);
    $error = $controller->login();
}

require_once __DIR__ . '/app/views/auth/login.php';