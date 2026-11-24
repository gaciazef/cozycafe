<?php

declare(strict_types=1);

final class Notification
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allForUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, transaction_id, title, message, is_read, created_at
             FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC, id DESC'
        );
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }

    public function unreadCount(int $userId): int
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0'
        );
        $statement->execute([$userId]);
        return (int) $statement->fetchColumn();
    }

    public function markRead(int $notificationId, int $userId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE notifications SET is_read = 1
             WHERE id = ? AND user_id = ?'
        );
        $statement->execute([$notificationId, $userId]);
    }

    public function markAllRead(int $userId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE notifications SET is_read = 1 WHERE user_id = ?'
        );
        $statement->execute([$userId]);
    }
}
