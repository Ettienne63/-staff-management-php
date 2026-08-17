<?php

$host = 'localhost';
$dbname = 'staff_management';
$username = 'root';
$password = '';

try {
    $connection = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $exception) {
    echo 'Database connection failed';
}