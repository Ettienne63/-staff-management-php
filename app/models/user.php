<?php

class User
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'email' => $email
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(string $email, string $password): void
    {
        $sql = 'INSERT INTO users (email, password) VALUES (:email, :password) ON DUPLICATE KEY UPDATE password = VALUES(password)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'email' => trim($email),
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}