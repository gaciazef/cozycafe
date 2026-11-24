<?php

declare(strict_types=1);

final class CafeSetting
{
    public function __construct(private PDO $pdo)
    {
    }

    public function get(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, cafe_name, logo, description, address, phone, banner
             FROM cafe_settings WHERE id = 1 LIMIT 1'
        );

        $settings = $statement->fetch();

        return $settings ?: [
            'cafe_name' => 'CozyCafe',
            'logo' => null,
            'description' => 'Nikmati menu favorit dan suasana yang hangat.',
            'address' => '',
            'phone' => '',
            'banner' => null,
        ];
    }
}
