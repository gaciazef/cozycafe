<?php

declare(strict_types=1);

final class CafeTable
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAvailable(int $guestCount, string $date, string $startTime, string $endTime): array
    {
        $statement = $this->pdo->prepare(
            'SELECT ct.id, ct.table_number, ct.area, ct.capacity
             FROM cafe_tables ct
             WHERE ct.status = ?
               AND ct.capacity >= ?
               AND NOT EXISTS (
                   SELECT 1
                   FROM bookings b
                   WHERE b.table_id = ct.id
                     AND b.booking_date = ?
                     AND b.status <> ?
                     AND b.start_time < ?
                     AND b.end_time > ?
               )
             ORDER BY ct.capacity ASC, ct.table_number ASC'
        );
        $statement->execute([
            'Aktif',
            $guestCount,
            $date,
            'Dibatalkan',
            $endTime,
            $startTime,
        ]);

        return $statement->fetchAll();
    }

    public function findById(int $tableId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, table_number, area, capacity, status
             FROM cafe_tables WHERE id = ?'
        );
        $statement->execute([$tableId]);
        $table = $statement->fetch();
        return $table ?: null;
    }
}
