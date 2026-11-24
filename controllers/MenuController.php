<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/Category.php';
require_once dirname(__DIR__) . '/models/Menu.php';
require_once dirname(__DIR__) . '/models/CafeSetting.php';

final class MenuController
{
    private Category $categories;
    private Menu $menus;
    private CafeSetting $settings;

    public function __construct(private PDO $pdo)
    {
        $this->categories = new Category($pdo);
        $this->menus = new Menu($pdo);
        $this->settings = new CafeSetting($pdo);
    }

    public function home(): void
    {
        $filters = $this->readFilters();
        $settings = $this->settings->get();
        $categories = $this->categories->all();
        $menus = $this->menus->search($filters);
        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/home.php';
    }

    public function menu(?string $kind = null): void
    {
        $filters = $this->readFilters();
        if ($kind === 'makanan' || $kind === 'minuman') {
            $filters['kind'] = $kind;
        }

        $settings = $this->settings->get();
        $categories = $this->categories->all();
        $menus = $this->menus->search($filters);
        $kindTitle = $kind === 'makanan'
            ? 'Menu Makanan'
            : ($kind === 'minuman' ? 'Menu Minuman' : 'Semua Menu');
        $kind = $kind === 'makanan' || $kind === 'minuman' ? $kind : null;
        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/menu.php';
    }

    private function readFilters(): array
    {
        return [
            'search' => trim((string) ($_GET['search'] ?? '')),
            'category_id' => ($_GET['category'] ?? '') !== '' ? (int) $_GET['category'] : null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'sort' => $_GET['sort'] ?? '',
            'available_only' => isset($_GET['available_only']),
            'kind' => null,
        ];
    }
}
