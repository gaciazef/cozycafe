<?php

declare(strict_types=1);

final class Menu
{
    public function __construct(private PDO $pdo)
    {
    }

    public function search(array $filters = []): array
    {
        $sql = 'SELECT m.id, m.category_id, m.name, m.description, m.price, m.image,
                       m.availability_status, c.name AS category_name
                FROM menus m
                INNER JOIN categories c ON c.id = m.category_id
                WHERE 1 = 1';
        $parameters = [];

        if ($search = trim((string) ($filters['search'] ?? ''))) {
            $sql .= ' AND m.name LIKE ?';
            $parameters[] = '%' . $search . '%';
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND m.category_id = ?';
            $parameters[] = (int) $filters['category_id'];
        }

        if (!empty($filters['kind'])) {
            $drinkCategories = ['Coffee', 'Non-Coffee'];
            if ($filters['kind'] === 'makanan') {
                $sql .= ' AND c.name NOT IN ("Coffee", "Non-Coffee")';
            } elseif ($filters['kind'] === 'minuman') {
                $placeholders = implode(',', array_fill(0, count($drinkCategories), '?'));
                $sql .= ' AND c.name IN (' . $placeholders . ')';
                array_push($parameters, ...$drinkCategories);
            }
        }

        if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
            $sql .= ' AND m.price >= ?';
            $parameters[] = (float) $filters['min_price'];
        }

        if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
            $sql .= ' AND m.price <= ?';
            $parameters[] = (float) $filters['max_price'];
        }

        if (!empty($filters['available_only'])) {
            $sql .= ' AND m.availability_status = ?';
            $parameters[] = 'Tersedia';
        }

        $sort = $filters['sort'] ?? '';
        $sql .= $sort === 'price_desc'
            ? ' ORDER BY m.price DESC, m.name ASC'
            : ($sort === 'price_asc'
                ? ' ORDER BY m.price ASC, m.name ASC'
                : ' ORDER BY m.name ASC');

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT m.*, c.name AS category_name
             FROM menus m
             INNER JOIN categories c ON c.id = m.category_id
             WHERE m.id = ? LIMIT 1'
        );
        $statement->execute([$id]);
        $menu = $statement->fetch();

        return $menu ?: null;
    }
}
