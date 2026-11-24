<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';

final class ReportController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function index(): void
    {
        require_admin();

        $today = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $tanggalMulai = trim((string) ($_GET['tanggal_mulai'] ?? $today->format('Y-m-01')));
        $tanggalAkhir = trim((string) ($_GET['tanggal_akhir'] ?? $today->format('Y-m-t')));

        $validStart = $this->validDate($tanggalMulai);
        $validEnd = $this->validDate($tanggalAkhir);
        if (!$validStart || !$validEnd || $tanggalMulai > $tanggalAkhir) {
            set_flash('warning', 'Rentang tanggal laporan tidak valid.');
            $tanggalMulai = $today->format('Y-m-01');
            $tanggalAkhir = $today->format('Y-m-t');
        }

        $statement = $this->pdo->prepare(
            'SELECT t.id, t.transaction_code, t.created_at, t.total_amount,
                    t.payment_status, u.name AS customer_name,
                    b.booking_date, b.start_time, b.end_time,
                    ct.table_number, ct.area,
                    b.status AS booking_status, o.order_status
             FROM transactions t
             INNER JOIN users u ON u.id = t.user_id
             INNER JOIN bookings b ON b.transaction_id = t.id
             INNER JOIN orders o ON o.transaction_id = t.id
             INNER JOIN cafe_tables ct ON ct.id = b.table_id
             WHERE DATE(t.created_at) >= ?
               AND DATE(t.created_at) <= ?
             ORDER BY t.created_at DESC, t.id DESC'
        );
        $statement->execute([$tanggalMulai, $tanggalAkhir]);
        $transactions = $statement->fetchAll();

        $totalPendapatan = 0.0;
        foreach ($transactions as $transaction) {
            if ($transaction['payment_status'] === 'Sudah Dibayar') {
                $totalPendapatan += (float) $transaction['total_amount'];
            }
        }

        $summary = [
            'total_transactions' => count($transactions),
            'total_revenue' => $totalPendapatan,
        ];
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/reports.php';
    }

    private function validDate(string $date): bool
    {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
