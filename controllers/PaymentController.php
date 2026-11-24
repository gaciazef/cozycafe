<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';

final class PaymentController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function index(): void
    {
        require_admin();
        $status = trim((string) ($_GET['status'] ?? ''));
        $sql = 'SELECT t.*,u.name AS customer_name
                FROM transactions t
                JOIN users u ON u.id=t.user_id
                JOIN bookings b ON b.transaction_id=t.id
                JOIN orders o ON o.transaction_id=t.id
                WHERE b.status <> "Dibatalkan"
                  AND o.order_status <> "Dibatalkan"';
        $parameters = [];
        if ($status === 'Belum Dibayar' || $status === 'Sudah Dibayar') {
            $sql .= ' AND t.payment_status=?';
            $parameters[] = $status;
        }
        $sql .= ' ORDER BY t.created_at DESC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        $transactions = $statement->fetchAll();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/payments.php';
    }

    public function confirm(): void
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan pembayaran tidak valid.');
            redirect('admin/payments');
        }
        $id = (int) ($_POST['transaction_id'] ?? 0);
        $check = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM transactions t
             JOIN bookings b ON b.transaction_id=t.id
             JOIN orders o ON o.transaction_id=t.id
             WHERE t.id=?
               AND b.status <> ?
               AND o.order_status <> ?'
        );
        $check->execute([$id, 'Dibatalkan', 'Dibatalkan']);
        if ((int) $check->fetchColumn() !== 1) {
            set_flash('danger', 'Transaksi yang dibatalkan tidak dapat dikonfirmasi sebagai dibayar.');
            redirect('admin/payments');
        }
        $statement = $this->pdo->prepare(
            'UPDATE transactions SET payment_status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
        );
        $statement->execute(['Sudah Dibayar', $id]);
        set_flash('success', 'Pembayaran telah dikonfirmasi.');
        redirect('admin/payments');
    }
}
