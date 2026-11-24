<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';

final class AdminController
{
    private User $users;

    public function __construct(private PDO $pdo)
    {
        $this->users = new User($pdo);
    }

    public function dashboard(): void
    {
        require_admin();
        $stats = [
            'customers' => (int) $this->pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
            'menus' => (int) $this->pdo->query('SELECT COUNT(*) FROM menus')->fetchColumn(),
            'bookings' => (int) $this->pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
            'orders' => (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'revenue' => (float) $this->pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM transactions WHERE payment_status='Sudah Dibayar'")->fetchColumn(),
        ];
        $daily = $this->pdo->query(
            "SELECT DATE(created_at) AS label, COUNT(*) AS total
             FROM transactions
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
             GROUP BY DATE(created_at)
             ORDER BY label ASC"
        )->fetchAll();
        $monthly = $this->pdo->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS label, COUNT(*) AS total
             FROM transactions
             WHERE created_at >= DATE_FORMAT(CURDATE(),'%Y-01-01')
             GROUP BY DATE_FORMAT(created_at,'%Y-%m')
             ORDER BY label ASC"
        )->fetchAll();
        $latestBookings = $this->pdo->query(
            'SELECT b.*, ct.table_number, ct.area, t.transaction_code
             FROM bookings b
             JOIN cafe_tables ct ON ct.id=b.table_id
             JOIN transactions t ON t.id=b.transaction_id
             ORDER BY b.created_at DESC LIMIT 5'
        )->fetchAll();
        $latestOrders = $this->pdo->query(
            'SELECT o.*, t.transaction_code
             FROM orders o
             JOIN transactions t ON t.id=o.transaction_id
             ORDER BY o.created_at DESC LIMIT 5'
        )->fetchAll();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/dashboard.php';
    }

    public function categories(): void
    {
        require_admin();
        $action = $_POST['action'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                set_flash('danger', 'Sesi formulir tidak valid.');
                redirect('admin/categories');
            }
            if ($action === 'create' || $action === 'update') {
                $name = trim((string) ($_POST['name'] ?? ''));
                $description = trim((string) ($_POST['description'] ?? ''));
                if ($name === '' || mb_strlen($name) > 100) {
                    set_flash('danger', 'Nama kategori wajib diisi dan maksimal 100 karakter.');
                    redirect('admin/categories');
                }
                try {
                    if ($action === 'create') {
                        $statement = $this->pdo->prepare(
                            'INSERT INTO categories(name,description) VALUES (?,?)'
                        );
                        $statement->execute([$name, $description]);
                        set_flash('success', 'Kategori berhasil ditambahkan.');
                    } else {
                        $id = (int) ($_POST['id'] ?? 0);
                        $statement = $this->pdo->prepare(
                            'UPDATE categories SET name=?,description=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                        );
                        $statement->execute([$name, $description, $id]);
                        set_flash('success', 'Kategori berhasil diperbarui.');
                    }
                } catch (PDOException $exception) {
                    set_flash('danger', 'Nama kategori sudah digunakan.');
                }
            } elseif ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $count = $this->pdo->prepare('SELECT COUNT(*) FROM menus WHERE category_id=?');
                $count->execute([$id]);
                if ((int) $count->fetchColumn() > 0) {
                    set_flash('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh menu.');
                } else {
                    $statement = $this->pdo->prepare('DELETE FROM categories WHERE id=?');
                    $statement->execute([$id]);
                    set_flash('success', 'Kategori berhasil dihapus.');
                }
            }
            redirect('admin/categories');
        }

        $categories = $this->pdo->query(
            'SELECT c.*, COUNT(m.id) AS menu_count
             FROM categories c LEFT JOIN menus m ON m.category_id=c.id
             GROUP BY c.id ORDER BY c.name ASC'
        )->fetchAll();
        $editCategory = null;
        if (!empty($_GET['edit'])) {
            $statement = $this->pdo->prepare('SELECT * FROM categories WHERE id=?');
            $statement->execute([(int) $_GET['edit']]);
            $editCategory = $statement->fetch() ?: null;
        }
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/categories.php';
    }

    public function menus(): void
    {
        require_admin();
        $action = $_POST['action'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                set_flash('danger', 'Sesi formulir tidak valid.');
                redirect('admin/menus');
            }
            if ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                $menu = $this->pdo->prepare('SELECT image FROM menus WHERE id=?');
                $menu->execute([$id]);
                $row = $menu->fetch();
                $statement = $this->pdo->prepare('DELETE FROM menus WHERE id=?');
                $statement->execute([$id]);
                if ($row && $row['image']) {
                    $path = dirname(__DIR__) . '/' . ltrim($row['image'], '/');
                    if (is_file($path) && str_starts_with(realpath($path) ?: '', dirname(__DIR__) . '/uploads/menu')) {
                        unlink($path);
                    }
                }
                set_flash('success', 'Menu berhasil dihapus.');
                redirect('admin/menus');
            }
            $id = (int) ($_POST['id'] ?? 0);
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $price = (float) ($_POST['price'] ?? -1);
            $status = ($_POST['availability_status'] ?? '') === 'Tidak Tersedia'
                ? 'Tidak Tersedia' : 'Tersedia';
            if ($categoryId < 1 || $name === '' || mb_strlen($name) > 150 || $price <= 0) {
                set_flash('danger', 'Nama, kategori, dan harga positif wajib diisi.');
                redirect('admin/menus');
            }

            $image = null;
            if (!empty($_FILES['image']['name'])) {
                [$image, $error] = upload_image($_FILES['image'], 'uploads/menu', 2);
                if ($error) {
                    set_flash('danger', $error);
                    redirect('admin/menus');
                }
            }

            if ($action === 'create') {
                $statement = $this->pdo->prepare(
                    'INSERT INTO menus(category_id,name,description,price,image,availability_status)
                     VALUES (?,?,?,?,?,?)'
                );
                $statement->execute([$categoryId, $name, $description, $price, $image, $status]);
                set_flash('success', 'Menu berhasil ditambahkan.');
            } elseif ($action === 'update') {
                $old = $this->pdo->prepare('SELECT image FROM menus WHERE id=?');
                $old->execute([$id]);
                $oldImage = $old->fetchColumn();
                if ($image) {
                    $statement = $this->pdo->prepare(
                        'UPDATE menus SET category_id=?,name=?,description=?,price=?,image=?,availability_status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                    );
                    $statement->execute([$categoryId, $name, $description, $price, $image, $status, $id]);
                    if ($oldImage) {
                        $oldPath = dirname(__DIR__) . '/' . ltrim((string) $oldImage, '/');
                        if (is_file($oldPath) && str_starts_with(realpath($oldPath) ?: '', dirname(__DIR__) . '/uploads/menu')) {
                            unlink($oldPath);
                        }
                    }
                } else {
                    $statement = $this->pdo->prepare(
                        'UPDATE menus SET category_id=?,name=?,description=?,price=?,availability_status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                    );
                    $statement->execute([$categoryId, $name, $description, $price, $status, $id]);
                }
                set_flash('success', 'Menu berhasil diperbarui.');
            }
            redirect('admin/menus');
        }

        $search = trim((string) ($_GET['search'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $count = $this->pdo->prepare("SELECT COUNT(*) FROM menus WHERE name LIKE ?");
        $count->execute(['%' . $search . '%']);
        $totalPages = max(1, (int) ceil((int) $count->fetchColumn() / $perPage));
        $statement = $this->pdo->prepare(
            'SELECT m.*,c.name AS category_name FROM menus m
             JOIN categories c ON c.id=m.category_id
             WHERE m.name LIKE ? ORDER BY m.name ASC LIMIT ? OFFSET ?'
        );
        $statement->bindValue(1, '%' . $search . '%');
        $statement->bindValue(2, $perPage, PDO::PARAM_INT);
        $statement->bindValue(3, $offset, PDO::PARAM_INT);
        $statement->execute();
        $menus = $statement->fetchAll();
        $categories = $this->pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
        $editMenu = null;
        if (!empty($_GET['edit'])) {
            $statement = $this->pdo->prepare('SELECT * FROM menus WHERE id=?');
            $statement->execute([(int) $_GET['edit']]);
            $editMenu = $statement->fetch() ?: null;
        }
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/menus.php';
    }

    public function tables(): void
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                set_flash('danger', 'Sesi formulir tidak valid.');
                redirect('admin/tables');
            }
            $action = (string) ($_POST['action'] ?? '');
            $id = (int) ($_POST['id'] ?? 0);
            if ($action === 'delete') {
                try {
                    $statement = $this->pdo->prepare('DELETE FROM cafe_tables WHERE id=?');
                    $statement->execute([$id]);
                    set_flash('success', 'Meja berhasil dihapus.');
                } catch (PDOException $exception) {
                    set_flash('danger', 'Meja tidak dapat dihapus karena digunakan oleh riwayat booking.');
                }
                redirect('admin/tables');
            }
            $number = trim((string) ($_POST['table_number'] ?? ''));
            $area = ($_POST['area'] ?? '') === 'Outdoor' ? 'Outdoor' : 'Indoor';
            $capacity = (int) ($_POST['capacity'] ?? 0);
            $status = ($_POST['status'] ?? '') === 'Tidak Aktif' ? 'Tidak Aktif' : 'Aktif';
            if ($number === '' || $capacity < 1) {
                set_flash('danger', 'Nomor meja dan kapasitas minimal 1 wajib diisi.');
                redirect('admin/tables');
            }
            try {
                if ($action === 'create') {
                    $statement = $this->pdo->prepare(
                        'INSERT INTO cafe_tables(table_number,area,capacity,status) VALUES (?,?,?,?)'
                    );
                    $statement->execute([$number, $area, $capacity, $status]);
                } else {
                    $statement = $this->pdo->prepare(
                        'UPDATE cafe_tables SET table_number=?,area=?,capacity=?,status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                    );
                    $statement->execute([$number, $area, $capacity, $status, $id]);
                }
                set_flash('success', 'Data meja berhasil disimpan.');
            } catch (PDOException $exception) {
                set_flash('danger', 'Nomor meja sudah digunakan.');
            }
            redirect('admin/tables');
        }
        $tables = $this->pdo->query('SELECT * FROM cafe_tables ORDER BY table_number')->fetchAll();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/tables.php';
    }

    public function bookings(): void
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                set_flash('danger', 'Sesi formulir tidak valid.');
                redirect('admin/bookings');
            }
            $action = (string) ($_POST['action'] ?? '');
            $id = (int) ($_POST['booking_id'] ?? 0);
            if ($action === 'finish') {
                $statement = $this->pdo->prepare(
                    'UPDATE bookings SET status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                );
                $statement->execute(['Selesai', $id]);
                set_flash('success', 'Booking berhasil ditandai selesai.');
            } elseif ($action === 'cancel') {
                $reason = trim((string) ($_POST['reason'] ?? ''));
                if ($reason === '') {
                    set_flash('danger', 'Alasan pembatalan wajib diisi.');
                    redirect('admin/bookings');
                }
                $this->pdo->beginTransaction();
                try {
                    $booking = $this->pdo->prepare(
                        'SELECT transaction_id FROM bookings WHERE id=? FOR UPDATE'
                    );
                    $booking->execute([$id]);
                    $transactionId = (int) $booking->fetchColumn();
                    if ($transactionId < 1) {
                        throw new RuntimeException('Booking tidak ditemukan.');
                    }
                    $this->pdo->prepare(
                        'UPDATE bookings SET status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                    )->execute(['Dibatalkan', $id]);
                    $this->pdo->prepare(
                        'UPDATE orders SET order_status=?,updated_at=CURRENT_TIMESTAMP WHERE transaction_id=?'
                    )->execute(['Dibatalkan', $transactionId]);
                    $this->pdo->prepare(
                        'INSERT INTO cancellation_histories(transaction_id,canceled_by,reason) VALUES (?,?,?)'
                    )->execute([$transactionId, 'admin', $reason]);
                    $user = $this->pdo->prepare(
                        'SELECT user_id FROM transactions WHERE id=?'
                    );
                    $user->execute([$transactionId]);
                    $userId = (int) $user->fetchColumn();
                    $this->pdo->prepare(
                        'INSERT INTO notifications(user_id,transaction_id,title,message,is_read) VALUES (?,?,?,?,0)'
                    )->execute([
                        $userId,
                        $transactionId,
                        'Booking Dibatalkan',
                        'Booking dan pesanan terkait telah dibatalkan oleh admin.',
                    ]);
                    $this->pdo->commit();
                    set_flash('success', 'Booking berhasil dibatalkan.');
                } catch (Throwable $exception) {
                    if ($this->pdo->inTransaction()) {
                        $this->pdo->rollBack();
                    }
                    set_flash('danger', 'Pembatalan booking gagal.');
                }
            }
            redirect('admin/bookings');
        }

        $date = trim((string) ($_GET['date'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $sql = 'SELECT b.*,t.transaction_code,t.user_id,u.name AS customer_name,
                       ct.table_number,ct.area
                FROM bookings b
                JOIN transactions t ON t.id=b.transaction_id
                JOIN users u ON u.id=t.user_id
                JOIN cafe_tables ct ON ct.id=b.table_id
                WHERE 1=1';
        $parameters = [];
        if ($date !== '') {
            $sql .= ' AND b.booking_date=?';
            $parameters[] = $date;
        }
        if ($status !== '') {
            $sql .= ' AND b.status=?';
            $parameters[] = $status;
        }
        $sql .= ' ORDER BY b.booking_date DESC,b.start_time DESC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        $bookings = $statement->fetchAll();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/bookings.php';
    }

    public function customers(): void
    {
        require_admin();
        $search = trim((string) ($_GET['search'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $count = $this->pdo->prepare(
            "SELECT COUNT(*) FROM users WHERE role='customer' AND (name LIKE ? OR email LIKE ?)"
        );
        $like = '%' . $search . '%';
        $count->execute([$like, $like]);
        $totalPages = max(1, (int) ceil((int) $count->fetchColumn() / $perPage));
        $statement = $this->pdo->prepare(
            "SELECT u.*,(SELECT COUNT(*) FROM transactions t WHERE t.user_id=u.id) AS transaction_count
             FROM users u
             WHERE u.role='customer' AND (u.name LIKE ? OR u.email LIKE ?)
             ORDER BY u.created_at DESC LIMIT ? OFFSET ?"
        );
        $statement->bindValue(1, $like);
        $statement->bindValue(2, $like);
        $statement->bindValue(3, $perPage, PDO::PARAM_INT);
        $statement->bindValue(4, $offset, PDO::PARAM_INT);
        $statement->execute();
        $customers = $statement->fetchAll();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/customers.php';
    }

    public function settings(): void
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                set_flash('danger', 'Sesi formulir tidak valid.');
                redirect('admin/settings');
            }
            $cafeName = trim((string) ($_POST['cafe_name'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $address = trim((string) ($_POST['address'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            if ($cafeName === '' || mb_strlen($cafeName) > 150) {
                set_flash('danger', 'Nama cafe wajib diisi.');
                redirect('admin/settings');
            }
            $logo = null;
            $banner = null;
            if (!empty($_FILES['logo']['name'])) {
                [$logo, $error] = upload_image($_FILES['logo'], 'uploads/cafe', 2);
                if ($error) {
                    set_flash('danger', $error);
                    redirect('admin/settings');
                }
            }
            if (!empty($_FILES['banner']['name'])) {
                [$banner, $error] = upload_image($_FILES['banner'], 'uploads/cafe', 2);
                if ($error) {
                    set_flash('danger', $error);
                    redirect('admin/settings');
                }
            }
            $old = $this->pdo->query('SELECT logo,banner FROM cafe_settings WHERE id=1')->fetch();
            $logoValue = $logo ?: ($old['logo'] ?? null);
            $bannerValue = $banner ?: ($old['banner'] ?? null);
            $statement = $this->pdo->prepare(
                'UPDATE cafe_settings SET cafe_name=?,logo=?,description=?,address=?,phone=?,banner=?,updated_at=CURRENT_TIMESTAMP WHERE id=1'
            );
            $statement->execute([$cafeName, $logoValue, $description, $address, $phone, $bannerValue]);
            set_flash('success', 'Pengaturan cafe berhasil disimpan.');
            redirect('admin/settings');
        }
        $settings = $this->pdo->query('SELECT * FROM cafe_settings WHERE id=1')->fetch();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/settings.php';
    }

    public function adminProfile(): void
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                set_flash('danger', 'Sesi formulir tidak valid.');
                redirect('admin/settings');
            }
            $id = (int) current_user()['id'];
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            $password = (string) ($_POST['password'] ?? '');
            $current = $this->users->findById($id);
            $errors = [];
            if ($name === '' || $this->users->emailExists($email, $id) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Nama atau email tidak valid atau sudah digunakan.';
            }
            if ($password !== '' && mb_strlen($password) < 8) {
                $errors[] = 'Password baru minimal 8 karakter.';
            }
            if ($errors !== []) {
                set_flash('danger', implode(' ', $errors));
            } else {
                $this->users->updateProfile($id, $name, $email, $current['phone'] ?? '');
                if ($password !== '') {
                    $this->users->updatePassword($id, $password);
                }
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                set_flash('success', 'Profil administrator berhasil diperbarui.');
            }
        }
        redirect('admin/settings');
    }
}
