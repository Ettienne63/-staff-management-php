<?php

class Staff
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAll()
    {
        $sql = 'SELECT * FROM staff_members ORDER BY id DESC';

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM staff_members WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    public function update($id,$firstName,$lastName,$email,$department,$position)
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
    public function delete($id)
    {
        $sql = 'DELETE FROM staff_members WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);
    }
}



