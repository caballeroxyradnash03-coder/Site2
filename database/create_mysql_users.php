<?php
// Creates `users` table in MySQL database `site1` using credentials from .env
try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'site1';
    $user = 'root';
    $pass = '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(60) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `gender` VARCHAR(10) DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "users table created\n";
} catch (Exception $e) {
    echo "error: " . $e->getMessage() . "\n";
}
