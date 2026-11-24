<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

$dbHost = envValue('DB_HOST', 'localhost');
$dbPort = envValue('DB_PORT', '3306');
$dbName = envValue('DB_NAME', 'cozycafe');
$dbUser = envValue('DB_USER', 'root');
$dbPass = envValue('DB_PASS', '');

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $dbHost,
    $dbPort,
    $dbName
);

try {
    $pdo = new PDO(
        $dsn,
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $exception) {
    http_response_code(500);
    throw new RuntimeException('Tidak dapat terhubung ke database.', 0, $exception);
}
