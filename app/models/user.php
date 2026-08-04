<?php

class User
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'email' => $email
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}