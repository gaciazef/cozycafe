<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';

final class BackupController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function page(): void
    {
        require_admin();
        $flash = get_flash();
        require dirname(__DIR__) . '/views/admin/backup.php';
    }

    public function download(): void
    {
        require_admin();

        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
        $filename = 'cozycafe_backup_' . $now->format('Ymd_His') . '.sql';

        $this->pdo->beginTransaction();

        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "-- CozyCafe Database Backup\n";
        echo "-- Dibuat: " . $now->format('Y-m-d H:i:s') . " WIB\n";
        echo "-- Generator: Pure PHP PDO\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n";
        echo "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        echo "SET NAMES utf8mb4;\n\n";

        $tables = $this->pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $identifier = $this->quoteIdentifier((string) $table);
            $create = $this->pdo->query('SHOW CREATE TABLE ' . $identifier)->fetch(PDO::FETCH_ASSOC);
            $createSql = $create['Create Table'] ?? null;

            if (!is_string($createSql)) {
                continue;
            }

            echo "\n-- Struktur tabel `" . str_replace('`', '``', (string) $table) . "`\n";
            echo 'DROP TABLE IF EXISTS ' . $identifier . ";\n";
            echo $createSql . ";\n";

            $rows = $this->pdo->query('SELECT * FROM ' . $identifier)->fetchAll(PDO::FETCH_ASSOC);
            if ($rows === []) {
                continue;
            }

            echo "\n-- Data tabel `" . str_replace('`', '``', (string) $table) . "`\n";

            foreach ($rows as $row) {
                $columns = array_keys($row);
                $quotedColumns = array_map(
                    fn(string $column): string => '`' . str_replace('`', '``', $column) . '`',
                    $columns
                );

                $values = [];
                foreach ($columns as $column) {
                    $values[] = $this->quoteValue($row[$column]);
                }

                echo 'INSERT INTO ' . $identifier . ' (' . implode(',', $quotedColumns) . ') VALUES ('
                    . implode(',', $values) . ");\n";
            }
        }

        echo "\nSET FOREIGN_KEY_CHECKS=1;\n";
        $this->pdo->commit();
        exit;
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function quoteValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_int($value)) {
            return (string) $value;
        }
        if (is_float($value)) {
            return rtrim(rtrim(sprintf('%.12F', $value), '0'), '.');
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        $escaped = str_replace(
            ["\\", "\0", "\n", "\r", "'", '"', "\x1a"],
            ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"],
            (string) $value
        );
        return "'" . $escaped . "'";
    }
}
