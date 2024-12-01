<?php

namespace App\Repositories;

use App\Database;

class GroupRepository
{
    public function create($name)
    {
        $db = Database::connect();
        $stmt = $db->prepare('INSERT INTO groups (name) VALUES (:name)');
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public function getAll()
    {
        $db = Database::connect();
        $stmt = $db->query('SELECT * FROM groups');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM groups WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
