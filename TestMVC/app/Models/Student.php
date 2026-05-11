<?php

class Student
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all()
    {
        $stmt = $this->db->query('SELECT * FROM students ORDER BY id DESC');

        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO students (name, email, phone, major)
             VALUES (:name, :email, :phone, :major)'
        );

        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'major' => $data['major'],
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE students
             SET name = :name, email = :email, phone = :phone, major = :major
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'major' => $data['major'],
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM students WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}

