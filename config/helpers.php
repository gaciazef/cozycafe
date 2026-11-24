<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token = null): bool
{
    $submitted = $token ?? ($_POST['csrf_token'] ?? '');
    return is_string($submitted)
        && $submitted !== ''
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $submitted);
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function format_rupiah(int|float|string $angka): string
{
    return 'Rp' . number_format((float) $angka, 0, ',', '.');
}

function format_tanggal(?string $tanggal, bool $withTime = false): string
{
    if ($tanggal === null || $tanggal === '') {
        return '-';
    }

    $format = $withTime ? 'd/m/Y H:i' : 'd/m/Y';
    $date = new DateTimeImmutable($tanggal, new DateTimeZone('Asia/Jakarta'));
    return $date->format($format);
}

function format_waktu(?string $waktu): string
{
    if ($waktu === null || $waktu === '') {
        return '-';
    }

    $parts = explode(':', $waktu);
    return count($parts) >= 2 ? $parts[0] . '.' . $parts[1] : $waktu;
}

function redirect(string $path): never
{
    if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
        $path = BASE_URL . '/' . ltrim($path, '/');
    }
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']['id']);
}

function current_user(): ?array
{
    return is_array($_SESSION['user'] ?? null) ? $_SESSION['user'] : null;
}

function require_customer(): void
{
    $user = current_user();
    if (!$user || ($user['role'] ?? '') !== 'customer') {
        set_flash('warning', 'Silakan login sebagai pelanggan.');
        redirect('auth/login');
    }
}

function require_admin(): void
{
    $user = current_user();
    if (!$user || ($user['role'] ?? '') !== 'admin') {
        set_flash('warning', 'Akses ini hanya untuk administrator.');
        redirect('auth/login');
    }
}

function upload_image(array $file, string $targetDir, int $maxSizeMb = 2): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return [null, 'Tidak ada berkas yang berhasil diunggah atau terjadi kesalahan upload.'];
    }

    if (($file['size'] ?? 0) <= 0 || ($file['size'] ?? 0) > $maxSizeMb * 1024 * 1024) {
        return [null, 'Ukuran gambar maksimal ' . $maxSizeMb . ' MB.'];
    }

    $originalName = basename((string) ($file['name'] ?? ''));
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $allowedExtensions, true)) {
        return [null, 'Format gambar harus JPG, JPEG, PNG, atau WEBP.'];
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpName);
    $allowedMimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
    ];
    if (($allowedMimes[$extension] ?? null) !== $mimeType) {
        return [null, 'Tipe berkas gambar tidak valid.'];
    }

    $root = dirname(__DIR__);
    $targetDir = ltrim(str_replace('\\', '/', $targetDir), '/');
    $destination = $root . '/' . $targetDir;
    if (!is_dir($destination) && !mkdir($destination, 0775, true) && !is_dir($destination)) {
        return [null, 'Folder penyimpanan gambar tidak dapat dibuat.'];
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $destinationPath = rtrim($destination, '/') . '/' . $filename;
    if (!move_uploaded_file($tmpName, $destinationPath)) {
        return [null, 'Gambar gagal disimpan.'];
    }

    return [$targetDir . '/' . $filename, null];
}
