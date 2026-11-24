<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/Transaction.php';
require_once dirname(__DIR__) . '/models/Order.php';

final class TransactionController
{
    private Transaction $transactions;
    private Order $orders;

    public function __construct(private PDO $pdo)
    {
        $this->transactions = new Transaction($pdo);
        $this->orders = new Order($pdo);
    }

    public function index(): void
    {
        require_customer();
        $userId = (int) current_user()['id'];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $data = $this->transactions->paginateForUser($userId, $page, 10);

        $itemsByOrder = [];
        foreach ($data['transactions'] as &$transaction) {
            $orderIdStatement = $this->pdo->prepare(
                'SELECT id FROM orders WHERE transaction_id = ?'
            );
            $orderIdStatement->execute([(int) $transaction['id']]);
            $orderId = (int) $orderIdStatement->fetchColumn();
            $transaction['items'] = $orderId > 0
                ? $this->orders->items($orderId)
                : [];
        }
        unset($transaction);

        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $transactions = $data['transactions'];
        $totalPages = $data['pages'];
        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/transactions.php';
    }

    public function detail(int $transactionId): void
    {
        require_customer();
        $userId = (int) current_user()['id'];
        $transaction = $this->transactions->findByIdForUser($transactionId, $userId);
        if (!$transaction) {
            http_response_code(404);
            set_flash('danger', 'Transaksi tidak ditemukan.');
            redirect('transactions');
        }

        $items = $this->transactions->items((int) $transaction['order_id']);
        $changes = $this->transactions->changeHistory((int) $transaction['order_id']);
        $cancellation = $this->transactions->cancellationHistory($transactionId);
        $canCancel = $this->canCustomerCancel($transaction);
        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/transaction_detail.php';
    }

    public function cancel(int $transactionId): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan pembatalan tidak valid.');
            redirect('transactions');
        }

        $reason = trim((string) ($_POST['reason'] ?? 'Dibatalkan oleh pelanggan'));
        $userId = (int) current_user()['id'];
        $transaction = $this->transactions->findByIdForUser($transactionId, $userId);
        if (!$transaction) {
            set_flash('danger', 'Transaksi tidak ditemukan.');
            redirect('transactions');
        }
        if (!$this->canCustomerCancel($transaction)) {
            set_flash('danger', 'Transaksi tidak dapat dibatalkan kurang dari 2 jam sebelum jadwal atau karena statusnya sudah tidak aktif.');
            redirect('transactions/detail/' . $transactionId);
        }

        try {
            $this->pdo->beginTransaction();

            $orderStatement = $this->pdo->prepare(
                'SELECT id FROM orders WHERE transaction_id = ? FOR UPDATE'
            );
            $orderStatement->execute([$transactionId]);
            $order = $orderStatement->fetch();
            if (!$order) {
                throw new RuntimeException('Pesanan tidak ditemukan.');
            }

            $bookingStatement = $this->pdo->prepare(
                'UPDATE bookings SET status = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE transaction_id = ? AND status = ?'
            );
            $bookingStatement->execute([
                'Dibatalkan',
                $transactionId,
                'Dikonfirmasi',
            ]);

            $orderUpdate = $this->pdo->prepare(
                'UPDATE orders SET order_status = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?'
            );
            $orderUpdate->execute(['Dibatalkan', (int) $order['id']]);

            $historyStatement = $this->pdo->prepare(
                'INSERT INTO cancellation_histories
                 (transaction_id, canceled_by, reason)
                 VALUES (?, ?, ?)'
            );
            $historyStatement->execute([
                $transactionId,
                'customer',
                $reason,
            ]);

            $notificationStatement = $this->pdo->prepare(
                'INSERT INTO notifications
                 (user_id, transaction_id, title, message, is_read)
                 VALUES (?, ?, ?, ?, 0)'
            );
            $notificationStatement->execute([
                $userId,
                $transactionId,
                'Booking Dibatalkan',
                'Transaksi ' . $transaction['transaction_code'] . ' telah dibatalkan.',
            ]);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            set_flash('danger', 'Pembatalan gagal. Silakan coba lagi.');
            redirect('transactions/detail/' . $transactionId);
        }

        set_flash('success', 'Transaksi berhasil dibatalkan.');
        $notificationCount = $this->pdo->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0'
        );
        $notificationCount->execute([$userId]);
        $_SESSION['unread_notifications'] = (int) $notificationCount->fetchColumn();
        redirect('transactions/detail/' . $transactionId);
    }

    private function canCustomerCancel(array $transaction): bool
    {
        if (($transaction['booking_status'] ?? '') !== 'Dikonfirmasi'
            || ($transaction['order_status'] ?? '') === 'Dibatalkan'
        ) {
            return false;
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $start = DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i:s',
            $transaction['booking_date'] . ' ' . $transaction['start_time'],
            new DateTimeZone('Asia/Jakarta')
        );

        if ($start === false) {
            return false;
        }

        return $start->getTimestamp() - $now->getTimestamp() >= 2 * 60 * 60;
    }
}
