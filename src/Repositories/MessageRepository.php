<?php

namespace App\Repositories;

use App\Database;

class MessageRepository
{
    public function addMessage($groupId, $userId, $messageContent)
    {
        $db = Database::connect();
        $stmt = $db->prepare('INSERT INTO messages (group_id, user_id, message) VALUES (:group_id, :user_id, :message)');
        $stmt->bindValue(':group_id', $groupId);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':message', $messageContent);
        $stmt->execute();
    }

    public function getMessagesByGroup($groupId)
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM messages WHERE group_id = :group_id ORDER BY created_at DESC');
        $stmt->bindValue(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
