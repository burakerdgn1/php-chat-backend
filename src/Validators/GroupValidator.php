<?php

namespace App\Validators;

class GroupValidator
{
    //create group
    public static function validateGroupName($data): void
    {
        if (!isset($data['groupName']) || empty(trim($data['groupName']))) {
            throw new \InvalidArgumentException('Group name is required.');
        }
    }

    //join group
    public static function validateUserId($data): void
    {
        if (!isset($data['user_id']) || empty(trim($data['user_id']))) {
            throw new \InvalidArgumentException('User ID is required.');
        }
    }
}
