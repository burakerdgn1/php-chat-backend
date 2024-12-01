<?php

namespace App\Services;

use App\Repositories\MessageRepository;
use App\Repositories\UserGroupRepository;

class MessageService
{
    private $messageRepository;
    private $userGroupRepository;

    public function __construct(MessageRepository $messageRepository, UserGroupRepository $userGroupRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->userGroupRepository = $userGroupRepository;
    }

    public function postMessage($groupId, $userId, $messageContent)
    {
        // Check if the user is a member of the group
        if (!$this->userGroupRepository->isUserInGroup($groupId, $userId)) {
            throw new \Exception('User is not a member of this group.', 403);
        }

        // Insert the message into the database
        $this->messageRepository->addMessage($groupId, $userId, $messageContent);
    }

    public function getMessages($groupId, $userId)
    {
        // Check if the user is a member of the group
        if (!$this->userGroupRepository->isUserInGroup($groupId, $userId)) {
            throw new \Exception('User is not a member of this group.', 403);
        }

        // Retrieve all messages for the group
        return $this->messageRepository->getMessagesByGroup($groupId);
    }
}
