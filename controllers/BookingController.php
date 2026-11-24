<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/Cart.php';
require_once dirname(__DIR__) . '/models/CafeTable.php';
require_once dirname(__DIR__) . '/models/Booking.php';

final class BookingController
{
    private Cart $cart;
    private CafeTable $tables;
    private Booking $booking;

    public function __construct(private PDO $pdo)
    {
        $this->cart = new Cart($pdo);
        $this->tables = new CafeTable($pdo);
        $this->booking = new Booking($pdo);
    }

    public function show(): void
    {
        require_customer();
        $userId = (int) current_user()['id'];
        $items = $this->cart->items($userId);
        if ($items === []) {
            set_flash('warning', 'Keranjang masih kosong. Pilih minimal satu menu sebelum booking meja.');
            redirect('menu');
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $slots = [];
        for ($hour = 9; $hour <= 20; $hour++) {
            $start = sprintf('%02d:00:00', $hour);
            $slotTime = DateTimeImmutable::createFromFormat(
                '!Y-m-d H:i:s',
                $now->format('Y-m-d') . ' ' . $start,
                new DateTimeZone('Asia/Jakarta')
            );
            if ($slotTime !== false && $slotTime > $now) {
                $slots[] = [
                    'start' => $start,
                    'end' => sprintf('%02d:00:00', $hour + 2),
                    'label' => sprintf('%02d.00–%02d.00 WIB', $hour, $hour + 2),
                ];
            }
        }

        $guestCount = max(1, (int) ($_POST['guest_count'] ?? $_GET['guest_count'] ?? 1));
        $selectedSlot = (string) ($_POST['slot'] ?? $_GET['slot'] ?? '');
        $availableTables = [];
        $slotParts = null;

        if ($selectedSlot !== '') {
            foreach ($slots as $slot) {
                if ($slot['start'] === $selectedSlot) {
                    $slotParts = $slot;
                    break;
                }
            }
        }
        if ($slotParts !== null) {
            $availableTables = $this->tables->findAvailable(
                $guestCount,
                $now->format('Y-m-d'),
                $slotParts['start'],
                $slotParts['end']
            );
        }

        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/booking.php';
    }

    public function summary(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan booking tidak valid.');
            redirect('booking');
        }

        $guestCount = (int) ($_POST['guest_count'] ?? 0);
        $startTime = (string) ($_POST['start_time'] ?? '');
        $tableId = (int) ($_POST['table_id'] ?? 0);

        $allowedStarts = [];
        for ($hour = 9; $hour <= 20; $hour++) {
            $allowedStarts[] = sprintf('%02d:00:00', $hour);
        }
        if ($guestCount < 1 || $guestCount > 20 || !in_array($startTime, $allowedStarts, true)) {
            set_flash('danger', 'Jumlah tamu atau slot waktu tidak valid.');
            redirect('booking');
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $startHour = (int) substr($startTime, 0, 2);
        $startDateTime = DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i:s',
            $now->format('Y-m-d') . ' ' . $startTime,
            new DateTimeZone('Asia/Jakarta')
        );
        if ($startDateTime === false || $startDateTime <= $now) {
            set_flash('danger', 'Slot tersebut sudah lewat. Pilih slot yang masih tersedia.');
            redirect('booking');
        }

        $endTime = sprintf('%02d:00:00', $startHour + 2);
        $date = $now->format('Y-m-d');
        $table = $this->tables->findById($tableId);
        if (!$table
            || $table['status'] !== 'Aktif'
            || (int) $table['capacity'] < $guestCount
        ) {
            set_flash('danger', 'Meja yang dipilih tidak memenuhi kapasitas atau tidak aktif.');
            redirect('booking');
        }
        if ($this->booking->hasOverlap($tableId, $date, $startTime, $endTime)) {
            set_flash('danger', 'Meja tersebut baru saja tidak tersedia. Pilih meja atau slot lain.');
            redirect('booking');
        }

        $items = $this->cart->items((int) current_user()['id']);
        if ($items === []) {
            set_flash('warning', 'Keranjang masih kosong. Pilih minimal satu menu.');
            redirect('menu');
        }

        $_SESSION['checkout'] = [
            'booking_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'guest_count' => $guestCount,
            'table_id' => $tableId,
            'table_number' => $table['table_number'],
            'area' => $table['area'],
        ];

        $total = $this->cart->total($items);
        $checkout = $_SESSION['checkout'];
        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/checkout_summary.php';
    }

    public function confirm(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan konfirmasi tidak valid.');
            redirect('booking');
        }

        $checkout = $_SESSION['checkout'] ?? null;
        if (!is_array($checkout)) {
            set_flash('warning', 'Silakan pilih meja dan slot kembali.');
            redirect('booking');
        }

        $userId = (int) current_user()['id'];

        try {
            $this->pdo->beginTransaction();

            $cartIdStatement = $this->pdo->prepare('SELECT id FROM carts WHERE user_id = ? FOR UPDATE');
            $cartIdStatement->execute([$userId]);
            $cartRow = $cartIdStatement->fetch();
            if (!$cartRow) {
                throw new RuntimeException('Keranjang kosong.');
            }

            $cartItemsStatement = $this->pdo->prepare(
                'SELECT ci.menu_id, ci.quantity, ci.note, m.name, m.price, m.availability_status
                 FROM cart_items ci
                 INNER JOIN menus m ON m.id = ci.menu_id
                 WHERE ci.cart_id = ?
                 FOR UPDATE'
            );
            $cartItemsStatement->execute([(int) $cartRow['id']]);
            $cartItems = $cartItemsStatement->fetchAll();
            if ($cartItems === []) {
                throw new RuntimeException('Keranjang kosong.');
            }

            $total = 0.0;
            foreach ($cartItems as $item) {
                if ($item['availability_status'] !== 'Tersedia') {
                    throw new RuntimeException('Ada menu yang tidak tersedia. Hapus atau ganti menu tersebut.');
                }
                $total += (float) $item['price'] * (int) $item['quantity'];
            }

            $tableStatement = $this->pdo->prepare(
                'SELECT id, table_number, area, capacity, status
                 FROM cafe_tables WHERE id = ? FOR UPDATE'
            );
            $tableStatement->execute([(int) $checkout['table_id']]);
            $table = $tableStatement->fetch();
            if (!$table
                || $table['status'] !== 'Aktif'
                || (int) $table['capacity'] < (int) $checkout['guest_count']
            ) {
                throw new RuntimeException('Meja tidak tersedia atau kapasitas tidak mencukupi.');
            }

            $overlapStatement = $this->pdo->prepare(
                'SELECT 1 FROM bookings
                 WHERE table_id = ?
                   AND booking_date = ?
                   AND status <> ?
                   AND start_time < ?
                   AND end_time > ?
                 LIMIT 1
                 FOR UPDATE'
            );
            $overlapStatement->execute([
                (int) $checkout['table_id'],
                $checkout['booking_date'],
                'Dibatalkan',
                $checkout['end_time'],
                $checkout['start_time'],
            ]);
            if ($overlapStatement->fetchColumn()) {
                throw new RuntimeException('Meja baru saja dibooking. Silakan pilih meja lain.');
            }

            $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
            if ($checkout['booking_date'] !== $now->format('Y-m-d')) {
                throw new RuntimeException('Booking hanya dapat dilakukan untuk hari ini.');
            }

            $prefix = 'CC-' . $now->format('Ymd') . '-';
            $codeStatement = $this->pdo->prepare(
                'SELECT transaction_code
                 FROM transactions
                 WHERE transaction_code LIKE ?
                 ORDER BY transaction_code DESC
                 LIMIT 1
                 FOR UPDATE'
            );
            $codeStatement->execute([$prefix . '%']);
            $lastCode = $codeStatement->fetchColumn();
            $nextNumber = $lastCode ? ((int) substr((string) $lastCode, -3) + 1) : 1;
            $transactionCode = $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            $transactionStatement = $this->pdo->prepare(
                'INSERT INTO transactions
                 (transaction_code, user_id, total_amount, payment_status)
                 VALUES (?, ?, ?, ?)'
            );
            $transactionStatement->execute([
                $transactionCode,
                $userId,
                $total,
                'Belum Dibayar',
            ]);
            $transactionId = (int) $this->pdo->lastInsertId();

            $bookingStatement = $this->pdo->prepare(
                'INSERT INTO bookings
                 (transaction_id, table_id, booking_date, start_time, end_time, guest_count, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $bookingStatement->execute([
                $transactionId,
                (int) $checkout['table_id'],
                $checkout['booking_date'],
                $checkout['start_time'],
                $checkout['end_time'],
                (int) $checkout['guest_count'],
                'Dikonfirmasi',
            ]);

            $orderStatement = $this->pdo->prepare(
                'INSERT INTO orders (transaction_id, order_status, subtotal)
                 VALUES (?, ?, ?)'
            );
            $orderStatement->execute([$transactionId, 'Dikonfirmasi', $total]);
            $orderId = (int) $this->pdo->lastInsertId();

            $itemStatement = $this->pdo->prepare(
                'INSERT INTO order_items
                 (order_id, menu_id, menu_name_snapshot, price_snapshot, quantity, note, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            foreach ($cartItems as $item) {
                $quantity = (int) $item['quantity'];
                $price = (float) $item['price'];
                $itemStatement->execute([
                    $orderId,
                    (int) $item['menu_id'],
                    $item['name'],
                    $price,
                    $quantity,
                    $item['note'],
                    $price * $quantity,
                ]);
            }

            $notificationStatement = $this->pdo->prepare(
                'INSERT INTO notifications
                 (user_id, transaction_id, title, message, is_read)
                 VALUES (?, ?, ?, ?, 0)'
            );
            $notificationStatement->execute([
                $userId,
                $transactionId,
                'Booking Berhasil & Pesanan Dikonfirmasi',
                'Transaksi ' . $transactionCode . ' berhasil dibuat.',
            ]);

            $clearStatement = $this->pdo->prepare(
                'DELETE FROM cart_items WHERE cart_id = ?'
            );
            $clearStatement->execute([(int) $cartRow['id']]);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            set_flash('danger', $exception instanceof RuntimeException
                ? $exception->getMessage()
                : 'Transaksi gagal disimpan. Silakan coba lagi.');
            redirect('booking');
        }

        unset($_SESSION['checkout']);
        $_SESSION['cart_count'] = 0;
        if (($userId ?? 0) > 0) {
            $notificationCount = $this->pdo->prepare(
                'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0'
            );
            $notificationCount->execute([$userId]);
            $_SESSION['unread_notifications'] = (int) $notificationCount->fetchColumn();
        }
        $flash = null;
        require dirname(__DIR__) . '/views/customer/checkout_success.php';
    }
}
