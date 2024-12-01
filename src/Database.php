<?php

namespace App;

use PDO;

class Database {
    public static function connect() {
        $dbPath = __DIR__ . '/../data/chat.db';
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
