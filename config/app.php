<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');

const OPERATING_HOURS_START = '09:00';
const OPERATING_HOURS_END = '22:00';
const BOOKING_DURATION_HOURS = 2;

function envValue(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }

    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    return $default;
}

$appUrl = envValue('APP_URL');
if ($appUrl !== null) {
    define('BASE_URL', rtrim($appUrl, '/'));
} else {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $scriptDirectory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $basePath = $scriptDirectory === '.' ? '' : $scriptDirectory;
    define('BASE_URL', rtrim($scheme . '://' . $host . $basePath, '/'));
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}
