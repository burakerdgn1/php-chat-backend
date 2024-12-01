<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use App\Repositories\UserGroupRepository;

class GroupService
{
    private $groupRepository;
    private $userGroupRepository;

    public function __construct(GroupRepository $groupRepository, UserGroupRepository $userGroupRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->userGroupRepository = $userGroupRepository;
    }

    public function createGroup($name)
    {
        return $this->groupRepository->create($name);
    }

    public function getAllGroups()
    {
        return $this->groupRepository->getAll();
    }

    public function joinGroup($groupId, $userId)
    {
        if ($this->userGroupRepository->isUserInGroup($groupId, $userId)) {
            throw new \Exception('User is already a member of the group.');
        }

        $this->userGroupRepository->addUserToGroup($groupId, $userId);
    }

    public function getUserGroupsByUsername($username)
    {
        return $this->userGroupRepository->getUserGroupsByUsername($username);
    }
}
