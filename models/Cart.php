<?php

declare(strict_types=1);

final class Cart
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getOrCreate(int $userId): int
    {
        $statement = $this->pdo->prepare('SELECT id FROM carts WHERE user_id = ?');
        $statement->execute([$userId]);
        $cart = $statement->fetch();
        if ($cart) {
            return (int) $cart['id'];
        }

        $statement = $this->pdo->prepare('INSERT INTO carts (user_id) VALUES (?)');
        $statement->execute([$userId]);
        return (int) $this->pdo->lastInsertId();
    }

    public function items(int $userId): array
    {
        $cartId = $this->getOrCreate($userId);
        $statement = $this->pdo->prepare(
            'SELECT ci.id, ci.menu_id, ci.quantity, ci.note, m.name, m.price,
                    m.image, m.availability_status,
                    (ci.quantity * m.price) AS subtotal
             FROM cart_items ci
             INNER JOIN menus m ON m.id = ci.menu_id
             WHERE ci.cart_id = ?
             ORDER BY ci.created_at ASC'
        );
        $statement->execute([$cartId]);
        return $statement->fetchAll();
    }

    public function add(int $userId, int $menuId, int $quantity = 1, string $note = ''): array
    {
        $menu = $this->pdo->prepare(
            'SELECT id, name, availability_status FROM menus WHERE id = ?'
        );
        $menu->execute([$menuId]);
        $row = $menu->fetch();
        if (!$row) {
            return [false, 'Menu tidak ditemukan.'];
        }
        if ($row['availability_status'] !== 'Tersedia') {
            return [false, 'Menu tidak tersedia dan tidak dapat ditambahkan ke keranjang.'];
        }
        if ($quantity < 1 || $quantity > 99) {
            return [false, 'Kuantitas harus antara 1 sampai 99.'];
        }

        $cartId = $this->getOrCreate($userId);
        $existing = $this->pdo->prepare(
            'SELECT id, quantity FROM cart_items WHERE cart_id = ? AND menu_id = ?'
        );
        $existing->execute([$cartId, $menuId]);
        $item = $existing->fetch();

        if ($item) {
            $newQuantity = (int) $item['quantity'] + $quantity;
            if ($newQuantity > 99) {
                $newQuantity = 99;
            }
            $statement = $this->pdo->prepare(
                'UPDATE cart_items SET quantity = ?, note = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?'
            );
            $statement->execute([$newQuantity, $note, (int) $item['id']]);
        } else {
            $statement = $this->pdo->prepare(
                'INSERT INTO cart_items (cart_id, menu_id, quantity, note)
                 VALUES (?, ?, ?, ?)'
            );
            $statement->execute([$cartId, $menuId, $quantity, $note]);
        }

        return [true, $row['name'] . ' berhasil ditambahkan ke keranjang.'];
    }

    public function update(int $userId, int $itemId, int $quantity, string $note): array
    {
        if ($quantity < 1 || $quantity > 99) {
            return [false, 'Kuantitas harus antara 1 sampai 99.'];
        }

        $cartId = $this->getOrCreate($userId);
        $statement = $this->pdo->prepare(
            'UPDATE cart_items SET quantity = ?, note = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND cart_id = ?'
        );
        $statement->execute([$quantity, $note, $itemId, $cartId]);
        return [true, 'Item keranjang berhasil diperbarui.'];
    }

    public function remove(int $userId, int $itemId): void
    {
        $cartId = $this->getOrCreate($userId);
        $statement = $this->pdo->prepare(
            'DELETE FROM cart_items WHERE id = ? AND cart_id = ?'
        );
        $statement->execute([$itemId, $cartId]);
    }

    public function clear(int $userId): void
    {
        $cartId = $this->getOrCreate($userId);
        $statement = $this->pdo->prepare('DELETE FROM cart_items WHERE cart_id = ?');
        $statement->execute([$cartId]);
    }

    public function count(int $userId): int
    {
        $cartId = $this->getOrCreate($userId);
        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE cart_id = ?'
        );
        $statement->execute([$cartId]);
        return (int) $statement->fetchColumn();
    }

    public function total(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item['subtotal'];
        }
        return $total;
    }
}
