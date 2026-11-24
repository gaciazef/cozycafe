<?php

declare(strict_types=1);

final class Order
{
    public function __construct(private PDO $pdo)
    {
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
}
