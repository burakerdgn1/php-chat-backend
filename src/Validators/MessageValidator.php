<?php

namespace App\Validators;

class MessageValidator
{
    public static function validateUserId($data): void
    {
        if (!isset($data['user_id']) || empty(trim($data['user_id']))) {
            throw new \InvalidArgumentException('User ID is required.');
        }
    }

    public static function validateMessageContent($data): void
    {
        if (!isset($data['message']) || empty(trim($data['message']))) {
            throw new \InvalidArgumentException('Message content is required.');
        }
    }

    public static function validateUserName($userName): void
    {
        if (!isset($userName) || empty(trim($userName))) {
            throw new \InvalidArgumentException('User name is required.');
        }
    }
}
