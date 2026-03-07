<?php
// Make existing NULL genders set to 'Unknown', set default and NOT NULL, and add jobid column.
try {
    $host = '127.0.0.1';
    $port = 3306;
    $db = 'ddsbe2';
    $user = 'root';
    $pass = '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // 1) update NULL genders
    $pdo->exec("UPDATE `users` SET `gender` = 'Unknown' WHERE `gender` IS NULL");

    // 2) set default and NOT NULL
    $pdo->exec("ALTER TABLE `users` MODIFY `gender` VARCHAR(10) NOT NULL DEFAULT 'Unknown'");

    // 3) add jobid column if it doesn't exist
    $cols = $pdo->query("SHOW COLUMNS FROM `users`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('jobid', $cols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `jobid` INT UNSIGNED NOT NULL AFTER `gender`");
    }

    // 4) drop timestamp columns if they exist
    $dropCols = [];
    if (in_array('created_at', $cols)) $dropCols[] = 'created_at';
    if (in_array('updated_at', $cols)) $dropCols[] = 'updated_at';
    if (!empty($dropCols)) {
        $pdo->exec('ALTER TABLE `users` DROP COLUMN ' . implode(', DROP COLUMN ', $dropCols));
    }

    echo "alter completed\n";
} catch (Exception $e) {
    echo "error: " . $e->getMessage() . "\n";
}
