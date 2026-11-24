<?php

declare(strict_types=1);

final class Transaction
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByIdForUser(int $transactionId, int $userId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT t.*, b.table_id, b.booking_date, b.start_time, b.end_time,
                    b.guest_count, b.status AS booking_status,
                    o.id AS order_id, o.order_status, o.subtotal,
                    ct.table_number, ct.area
             FROM transactions t
             INNER JOIN bookings b ON b.transaction_id = t.id
             INNER JOIN orders o ON o.transaction_id = t.id
             INNER JOIN cafe_tables ct ON ct.id = b.table_id
             WHERE t.id = ? AND t.user_id = ?
             LIMIT 1'
        );
        $statement->execute([$transactionId, $userId]);
        $transaction = $statement->fetch();
        return $transaction ?: null;
    }

    public function paginateForUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $countStatement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM transactions WHERE user_id = ?'
        );
        $countStatement->execute([$userId]);
        $total = (int) $countStatement->fetchColumn();

        $statement = $this->pdo->prepare(
            'SELECT t.id, t.transaction_code, t.total_amount, t.payment_status, t.created_at,
                    b.booking_date, b.start_time, b.end_time, b.guest_count,
                    b.status AS booking_status, ct.table_number, ct.area,
                    o.order_status
             FROM transactions t
             INNER JOIN bookings b ON b.transaction_id = t.id
             INNER JOIN orders o ON o.transaction_id = t.id
             INNER JOIN cafe_tables ct ON ct.id = b.table_id
             WHERE t.user_id = ?
             ORDER BY t.created_at DESC, t.id DESC
             LIMIT ? OFFSET ?'
        );
        $statement->bindValue(1, $userId, PDO::PARAM_INT);
        $statement->bindValue(2, $perPage, PDO::PARAM_INT);
        $statement->bindValue(3, $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'transactions' => $statement->fetchAll(),
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function items(int $orderId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT menu_name_snapshot, price_snapshot, quantity, note, subtotal
             FROM order_items
             WHERE order_id = ?
             ORDER BY id ASC'
        );
        $statement->execute([$orderId]);
        return $statement->fetchAll();
    }

    public function cancellationHistory(int $transactionId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT canceled_by, reason, created_at
             FROM cancellation_histories
             WHERE transaction_id = ?
             ORDER BY created_at DESC
             LIMIT 1'
        );
        $statement->execute([$transactionId]);
        $history = $statement->fetch();
        return $history ?: null;
    }

    public function changeHistory(int $orderId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT change_description, old_total, new_total, created_at
             FROM order_change_histories
             WHERE order_id = ?
             ORDER BY created_at DESC, id DESC'
        );
        $statement->execute([$orderId]);
        return $statement->fetchAll();
    }
}
