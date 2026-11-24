<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';

require_admin();

$route = $_GET['route'] ?? 'dashboard';
$allowedRoutes = [
    'dashboard', 'categories', 'menus', 'tables', 'bookings', 'orders',
    'payments', 'customers', 'reports', 'settings', 'backup',
];

if (!in_array($route, $allowedRoutes, true)) {
    http_response_code(404);
    require dirname(__DIR__) . '/views/admin/not_found.php';
    exit;
}

set_flash('info', 'Fitur administrator akan tersedia pada tahap implementasi berikutnya.');
redirect('/');
