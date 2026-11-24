<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';

final class AuthController
{
    private User $users;

    public function __construct(private PDO $pdo)
    {
        $this->users = new User($pdo);
    }

    public function showLogin(): void
    {
        if (is_logged_in()) {
            $this->redirectByRole();
        }

        $flash = get_flash();
        require dirname(__DIR__) . '/views/auth/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/login');
        }
        if (!verify_csrf()) {
            set_flash('danger', 'Sesi formulir tidak valid. Silakan coba lagi.');
            redirect('auth/login');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if ($password === '') {
            $errors[] = 'Password wajib diisi.';
        }
        if ($errors !== []) {
            set_flash('danger', implode(' ', $errors));
            redirect('auth/login');
        }

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            set_flash('danger', 'Email atau password salah.');
            redirect('auth/login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
        ];
        if ($user['role'] === 'customer') {
            $notificationCount = $this->pdo->prepare(
                'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0'
            );
            $notificationCount->execute([(int) $user['id']]);
            $_SESSION['unread_notifications'] = (int) $notificationCount->fetchColumn();
        }

        set_flash('success', 'Berhasil masuk sebagai ' . $user['name'] . '.');
        $this->redirectByRole();
    }

    public function showRegister(): void
    {
        if (is_logged_in()) {
            $this->redirectByRole();
        }

        $flash = get_flash();
        require dirname(__DIR__) . '/views/auth/register.php';
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/register');
        }
        if (!verify_csrf()) {
            set_flash('danger', 'Sesi formulir tidak valid. Silakan coba lagi.');
            redirect('auth/register');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');
        $errors = [];

        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Nama lengkap wajib diisi dan maksimal 100 karakter.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            $errors[] = 'Format email tidak valid.';
        }
        if ($phone !== '' && mb_strlen($phone) > 30) {
            $errors[] = 'Nomor telepon maksimal 30 karakter.';
        }
        if (mb_strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }
        if (!hash_equals($password, $confirmation)) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }
        if ($email !== '' && $this->users->emailExists($email)) {
            $errors[] = 'Email sudah digunakan.';
        }
        if ($errors !== []) {
            set_flash('danger', implode(' ', $errors));
            redirect('auth/register');
        }

        $this->users->createCustomer($name, $email, $phone, $password);
        set_flash('success', 'Registrasi berhasil. Silakan masuk.');
        redirect('auth/login');
    }

    public function showProfile(): void
    {
        require_customer();

        $user = $this->users->findById((int) current_user()['id']);
        if (!$user) {
            $this->logout();
        }

        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/profile.php';
    }

    public function updateProfile(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('customer/profile');
        }
        if (!verify_csrf()) {
            set_flash('danger', 'Sesi formulir tidak valid. Silakan coba lagi.');
            redirect('customer/profile');
        }

        $id = (int) current_user()['id'];
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['new_password_confirmation'] ?? '');
        $errors = [];

        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Nama lengkap wajib diisi dan maksimal 100 karakter.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            $errors[] = 'Format email tidak valid.';
        }
        if ($phone !== '' && mb_strlen($phone) > 30) {
            $errors[] = 'Nomor telepon maksimal 30 karakter.';
        }
        if ($email !== '' && $this->users->emailExists($email, $id)) {
            $errors[] = 'Email sudah digunakan oleh akun lain.';
        }

        $user = $this->users->findById($id);
        if ($newPassword !== '' || $currentPassword !== '' || $confirmPassword !== '') {
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $errors[] = 'Password lama tidak sesuai.';
            }
            if (mb_strlen($newPassword) < 8) {
                $errors[] = 'Password baru minimal 8 karakter.';
            }
            if (!hash_equals($newPassword, $confirmPassword)) {
                $errors[] = 'Konfirmasi password baru tidak sama.';
            }
        }

        if ($errors !== []) {
            set_flash('danger', implode(' ', $errors));
            redirect('customer/profile');
        }

        $this->users->updateProfile($id, $name, $email, $phone);
        if ($newPassword !== '') {
            $this->users->updatePassword($id, $newPassword);
        }

        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone'] = $phone;
        set_flash('success', 'Profil berhasil diperbarui.');
        redirect('customer/profile');
    }

    public function logout(): never
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();

        session_start();
        set_flash('success', 'Anda telah keluar.');
        redirect('/');
    }

    private function redirectByRole(): never
    {
        $role = current_user()['role'] ?? null;
        redirect($role === 'admin' ? 'admin/dashboard' : 'customer/menu');
    }
}
