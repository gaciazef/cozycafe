<?php

declare(strict_types=1);

final class User
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, phone, password, role, created_at, updated_at
             FROM users WHERE email = ? LIMIT 1'
        );
        $statement->execute([$email]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, phone, password, role, created_at, updated_at
             FROM users WHERE id = ? LIMIT 1'
        );
        $statement->execute([$id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function createCustomer(string $name, string $email, string $phone, string $password): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (name, email, phone, password, role)
             VALUES (?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $name,
            $email,
            $phone,
            password_hash($password, PASSWORD_DEFAULT),
            'customer',
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $parameters = [$email];

        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $parameters[] = $exceptId;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn() > 0;
    }

    public function updateProfile(int $id, string $name, string $email, string $phone): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET name = ?, email = ?, phone = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ?'
        );

        return $statement->execute([$name, $email, $phone, $id]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
        );

        return $statement->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function listCustomers(int $limit = 50, int $offset = 0): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, phone, role, created_at, updated_at
             FROM users
             WHERE role = ?
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?'
        );
        $statement->bindValue(1, 'customer', PDO::PARAM_STR);
        $statement->bindValue(2, $limit, PDO::PARAM_INT);
        $statement->bindValue(3, $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
