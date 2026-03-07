<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=site1;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$count = $pdo->query('SELECT COUNT(*) FROM tbluserjob')->fetchColumn();
echo "site1 tbluserjob count=$count\n";
