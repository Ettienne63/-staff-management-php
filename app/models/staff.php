<?php

class Staff
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAll(): array
    {
        $sql = 'SELECT * FROM staff_members ORDER BY id DESC';

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = 'SELECT * FROM staff_members WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(string $firstName, string $lastName, string $email, string $department, string $position): void
    {
        $sql = 'INSERT INTO staff_members (first_name, last_name, email, department, position) VALUES (:first_name, :last_name, :email, :department, :position)';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'department' => $department,
            'position' => $position,
        ]);
    }

    public function update(int $id, string $firstName, string $lastName, string $email, string $department, string $position): void
    {
        $sql = 'UPDATE staff_members SET first_name = :first_name, last_name = :last_name, email = :email, department = :department, position = :position WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'department' => $department,
            'position' => $position
        ]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM staff_members WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);
    }
}



