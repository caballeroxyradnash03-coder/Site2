<?php
// Creates `users` table in MySQL database `ddsbe2` using credentials from .env
try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ddsbe2';
    $user = 'root';
    $pass = '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Ensure the jobs lookup table exists for the FK constraint.
    $pdo->exec("CREATE TABLE IF NOT EXISTS `tbluserjob` (
        `jobid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `description` TEXT NULL,
        PRIMARY KEY (`jobid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // If the job lookup table is empty, seed a few common jobs so jobid validation can pass.
    $count = $pdo->query("SELECT COUNT(*) FROM `tbluserjob`")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO `tbluserjob` (`name`, `description`) VALUES
            ('Developer', 'Software developer'),
            ('Manager', 'Project manager'),
            ('Tester', 'Quality assurance'),
            ('Admin', 'Administrator')");
    }

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(60) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `gender` VARCHAR(10) NOT NULL DEFAULT 'Unknown',
        `jobid` INT UNSIGNED NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        UNIQUE KEY `users_username_unique` (`username`),
        INDEX `users_jobid_index` (`jobid`),
        CONSTRAINT `users_jobid_fk` FOREIGN KEY (`jobid`) REFERENCES `tbluserjob` (`jobid`) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "users table created\n";
} catch (Exception $e) {
    echo "error: " . $e->getMessage() . "\n";
}
