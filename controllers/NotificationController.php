<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/Notification.php';

final class NotificationController
{
    private Notification $notifications;

    public function __construct(private PDO $pdo)
    {
        $this->notifications = new Notification($pdo);
    }

    public function index(): void
    {
        require_customer();
        $userId = (int) current_user()['id'];
        $notifications = $this->notifications->allForUser($userId);
        $_SESSION['unread_notifications'] = $this->notifications->unreadCount($userId);
        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/notifications.php';
    }

    public function markRead(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan notifikasi tidak valid.');
            redirect('notifications');
        }

        $userId = (int) current_user()['id'];
        $notificationId = (int) ($_POST['notification_id'] ?? 0);
        $transactionId = (int) ($_POST['transaction_id'] ?? 0);
        $this->notifications->markRead($notificationId, $userId);
        $_SESSION['unread_notifications'] = $this->notifications->unreadCount($userId);

        redirect($transactionId > 0
            ? 'transactions/detail/' . $transactionId
            : 'notifications');
    }

    public function markAllRead(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan notifikasi tidak valid.');
            redirect('notifications');
        }

        $userId = (int) current_user()['id'];
        $this->notifications->markAllRead($userId);
        $_SESSION['unread_notifications'] = 0;
        set_flash('success', 'Semua notifikasi telah ditandai sudah dibaca.');
        redirect('notifications');
    }
}
