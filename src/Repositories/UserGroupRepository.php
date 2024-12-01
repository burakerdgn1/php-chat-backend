<?php

namespace App\Repositories;

use App\Database;

class UserGroupRepository
{
    public function addUserToGroup($groupId, $userId)
    {
        $db = Database::connect();
        $stmt = $db->prepare('INSERT OR IGNORE INTO user_group (group_id, user_id) VALUES (:group_id, :user_id)');
        $stmt->bindValue(':group_id', $groupId);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
    }

    public function getUsersInGroup($groupId)
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT user_id FROM user_group WHERE group_id = :groupId');
        $stmt->bindValue(':groupId', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function isUserInGroup($groupId, $userId)
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT COUNT(*) FROM user_group WHERE group_id = :groupId AND user_id = :userId');
        $stmt->bindValue(':groupId', $groupId);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function getUserGroupsByUsername($username)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT g.* FROM user_group ug
             JOIN groups g ON ug.group_id = g.id
             WHERE ug.user_id = :username'
        );
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
