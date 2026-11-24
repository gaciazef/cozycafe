<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';

final class OrderController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function index(): void
    {
        require_admin();
        $status = trim((string) ($_GET['status'] ?? ''));
        $sql = 'SELECT o.*,t.transaction_code,t.user_id,u.name AS customer_name
                ,b.status AS booking_status
                FROM orders o
                JOIN transactions t ON t.id=o.transaction_id
                JOIN users u ON u.id=t.user_id
                JOIN bookings b ON b.transaction_id=t.id
                WHERE 1=1';
        $parameters = [];
        if ($status !== '') {
            $sql .= ' AND o.order_status=?';
            $parameters[] = $status;
        }
        $sql .= ' ORDER BY o.created_at DESC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        $orders = $statement->fetchAll();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/orders.php';
    }

    public function updateStatus(): void
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan status tidak valid.');
            redirect('admin/orders');
        }
        $id = (int) ($_POST['order_id'] ?? 0);
        $allowed = [
            'Menunggu Konfirmasi', 'Dikonfirmasi', 'Sedang Disiapkan',
            'Siap Disajikan', 'Selesai', 'Dibatalkan',
        ];
        $status = (string) ($_POST['order_status'] ?? '');
        if (!in_array($status, $allowed, true)) {
            set_flash('danger', 'Status pesanan tidak valid.');
            redirect('admin/orders');
        }
        $this->pdo->beginTransaction();
        try {
            $currentStatement = $this->pdo->prepare(
                'SELECT o.order_status,b.status AS booking_status
                 FROM orders o
                 JOIN bookings b ON b.transaction_id=o.transaction_id
                 WHERE o.id=? FOR UPDATE'
            );
            $currentStatement->execute([$id]);
            $current = $currentStatement->fetch();
            if (!$current) {
                throw new RuntimeException('Pesanan tidak ditemukan.');
            }

            $currentStatus = (string) $current['order_status'];
            $bookingStatus = (string) $current['booking_status'];
            if ($currentStatus === 'Dibatalkan' || $bookingStatus === 'Dibatalkan') {
                throw new RuntimeException('Pesanan yang dibatalkan tidak dapat diubah menjadi status aktif.');
            }
            if ($currentStatus === 'Selesai') {
                throw new RuntimeException('Pesanan yang sudah selesai tidak dapat diubah statusnya.');
            }
            if ($status === 'Dibatalkan') {
                throw new RuntimeException('Gunakan menu Kelola Booking untuk membatalkan transaksi beserta alasan.');
            }
            if ($status === $currentStatus) {
                $this->pdo->commit();
                set_flash('info', 'Status pesanan tidak berubah.');
                redirect('admin/orders');
            }

            $allowedNext = [
                'Menunggu Konfirmasi' => ['Dikonfirmasi'],
                'Dikonfirmasi' => ['Sedang Disiapkan', 'Siap Disajikan', 'Selesai'],
                'Sedang Disiapkan' => ['Siap Disajikan', 'Selesai'],
                'Siap Disajikan' => ['Selesai'],
            ];
            if (!in_array($status, $allowedNext[$currentStatus] ?? [], true)) {
                throw new RuntimeException(
                    'Status tidak dapat berubah dari "' . $currentStatus . '" menjadi "' . $status . '".'
                );
            }

            $statement = $this->pdo->prepare(
                'UPDATE orders SET order_status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
            );
            $statement->execute([$status, $id]);
            $target = $this->pdo->prepare(
                'SELECT t.id AS transaction_id,t.user_id,t.transaction_code
                 FROM orders o JOIN transactions t ON t.id=o.transaction_id WHERE o.id=?'
            );
            $target->execute([$id]);
            $targetRow = $target->fetch();
            if ($targetRow && in_array($status, ['Sedang Disiapkan', 'Siap Disajikan', 'Selesai'], true)) {
                $title = $status === 'Sedang Disiapkan'
                    ? 'Pesanan Sedang Disiapkan'
                    : ($status === 'Siap Disajikan' ? 'Pesanan Siap Disajikan' : 'Pesanan Selesai');
                $this->pdo->prepare(
                    'INSERT INTO notifications(user_id,transaction_id,title,message,is_read)
                     VALUES (?,?,?,?,0)'
                )->execute([
                    (int) $targetRow['user_id'],
                    (int) $targetRow['transaction_id'],
                    $title,
                    'Pesanan ' . $targetRow['transaction_code'] . ' kini berstatus ' . $status . '.',
                ]);
            }
            $this->pdo->commit();
            set_flash('success', 'Status pesanan berhasil diperbarui.');
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            set_flash('danger', $exception instanceof RuntimeException
                ? $exception->getMessage()
                : 'Status pesanan gagal diperbarui.');
        }
        redirect('admin/orders');
    }

    public function editDetail(): void
    {
        require_admin();
        $orderId = (int) ($_GET['id'] ?? 0);
        $order = $this->pdo->prepare(
            'SELECT o.*,t.transaction_code,t.total_amount,t.id AS transaction_id
             FROM orders o JOIN transactions t ON t.id=o.transaction_id WHERE o.id=?'
        );
        $order->execute([$orderId]);
        $orderRow = $order->fetch();
        if (!$orderRow) {
            set_flash('danger', 'Pesanan tidak ditemukan.');
            redirect('admin/orders');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                set_flash('danger', 'Sesi formulir tidak valid.');
                redirect('admin/orders/edit?id=' . $orderId);
            }
            $this->pdo->beginTransaction();
            try {
                $itemIds = $_POST['item_id'] ?? [];
                $quantities = $_POST['quantity'] ?? [];
                $notes = $_POST['note'] ?? [];
                $deleteIds = array_map('intval', $_POST['delete_item'] ?? []);
                $oldTotal = (float) $orderRow['subtotal'];
                $newTotal = 0.0;

                foreach ($itemIds as $index => $itemId) {
                    if (in_array((int) $itemId, $deleteIds, true)) {
                        continue;
                    }
                    $quantity = (int) ($quantities[$index] ?? 1);
                    $note = trim((string) ($notes[$index] ?? ''));
                    if ($quantity < 1) {
                        throw new RuntimeException('Kuantitas item minimal 1.');
                    }
                    $priceRow = $this->pdo->prepare(
                        'SELECT oi.price_snapshot FROM order_items oi WHERE oi.id=? AND oi.order_id=?'
                    );
                    $priceRow->execute([(int) $itemId, $orderId]);
                    $price = (float) $priceRow->fetchColumn();
                    $this->pdo->prepare(
                        'UPDATE order_items SET quantity=?,note=?,subtotal=? WHERE id=? AND order_id=?'
                    )->execute([$quantity, $note, $price * $quantity, (int) $itemId, $orderId]);
                    $newTotal += $price * $quantity;
                }

                $addMenuId = (int) ($_POST['add_menu_id'] ?? 0);
                $addQuantity = (int) ($_POST['add_quantity'] ?? 0);
                $addNote = trim((string) ($_POST['add_note'] ?? ''));
                if ($addMenuId > 0) {
                    $menu = $this->pdo->prepare(
                        'SELECT id,name,price,availability_status FROM menus WHERE id=?'
                    );
                    $menu->execute([$addMenuId]);
                    $menuRow = $menu->fetch();
                    if (!$menuRow || $menuRow['availability_status'] !== 'Tersedia' || $addQuantity < 1) {
                        throw new RuntimeException('Menu baru tidak valid atau tidak tersedia.');
                    }
                    $price = (float) $menuRow['price'];
                    $this->pdo->prepare(
                        'INSERT INTO order_items(order_id,menu_id,menu_name_snapshot,price_snapshot,quantity,note,subtotal)
                         VALUES (?,?,?,?,?,?,?)'
                    )->execute([
                        $orderId, $addMenuId, $menuRow['name'], $price,
                        $addQuantity, $addNote, $price * $addQuantity,
                    ]);
                    $newTotal += $price * $addQuantity;
                }

                foreach ($deleteIds as $deleteId) {
                    $this->pdo->prepare(
                        'DELETE FROM order_items WHERE id=? AND order_id=?'
                    )->execute([(int) $deleteId, $orderId]);
                }

                if ($newTotal < 0) {
                    $newTotal = 0;
                }
                $this->pdo->prepare(
                    'UPDATE orders SET subtotal=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                )->execute([$newTotal, $orderId]);
                $this->pdo->prepare(
                    'UPDATE transactions SET total_amount=?,updated_at=CURRENT_TIMESTAMP WHERE id=?'
                )->execute([$newTotal, (int) $orderRow['transaction_id']]);
                $this->pdo->prepare(
                    'INSERT INTO order_change_histories(order_id,admin_id,change_description,old_total,new_total)
                     VALUES (?,?,?,?,?)'
                )->execute([
                    $orderId,
                    (int) current_user()['id'],
                    'Admin mengubah detail pesanan',
                    $oldTotal,
                    $newTotal,
                ]);
                $this->pdo->commit();
                set_flash('success', 'Detail pesanan berhasil diperbarui.');
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                set_flash('danger', $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'Detail pesanan gagal diperbarui.');
            }
            redirect('admin/orders/edit?id=' . $orderId);
        }

        $itemsStatement = $this->pdo->prepare(
            'SELECT * FROM order_items WHERE order_id=? ORDER BY id'
        );
        $itemsStatement->execute([$orderId]);
        $items = $itemsStatement->fetchAll();
        $menus = $this->pdo->query(
            "SELECT id,name,price FROM menus WHERE availability_status='Tersedia' ORDER BY name"
        )->fetchAll();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/order_edit.php';
    }
}
