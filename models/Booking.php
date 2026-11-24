<?php

declare(strict_types=1);

final class Booking
{
    public function __construct(private PDO $pdo)
    {
    }

    public function hasOverlap(
        int $tableId,
        string $date,
        string $startTime,
        string $endTime
    ): bool {
        $statement = $this->pdo->prepare(
            'SELECT 1
             FROM bookings
             WHERE table_id = ?
               AND booking_date = ?
               AND status <> ?
               AND start_time < ?
               AND end_time > ?
             LIMIT 1'
        );
        $statement->execute([
            $tableId,
            $date,
            'Dibatalkan',
            $endTime,
            $startTime,
        ]);

        return (bool) $statement->fetchColumn();
    }
}
