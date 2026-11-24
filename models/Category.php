<?php

declare(strict_types=1);

final class Category
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, description
             FROM categories
             ORDER BY name ASC'
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, description
             FROM categories WHERE id = ? LIMIT 1'
        );
        $statement->execute([$id]);
        $category = $statement->fetch();

        return $category ?: null;
    }
}
