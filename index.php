<?php

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/helpers.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/', '/');
$projectPrefix = trim(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '/');
if ($projectPrefix !== '' && str_starts_with($path, $projectPrefix . '/')) {
    $path = substr($path, strlen($projectPrefix));
}
$path = trim($path, '/');

if ($path !== '' && str_starts_with($path, 'auth/')) {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController($pdo);
    $action = substr($path, 5);

    if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->register();
    }
    if ($action === 'register') {
        $controller->showRegister();
        exit;
    }
    if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->login();
    }
    if ($action === 'login') {
        $controller->showLogin();
        exit;
    }
    if ($action === 'logout') {
        $controller->logout();
    }

    http_response_code(404);
    exit;
}

if ($path === 'customer/profile') {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController($pdo);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->updateProfile();
    }
    $controller->showProfile();
    exit;
}

if ($path === 'customer/menu') {
    require_once __DIR__ . '/controllers/MenuController.php';
    $controller = new MenuController($pdo);
    $controller->menu();
    exit;
}

if ($path === 'menu' || $path === 'menu/makanan' || $path === 'menu/minuman') {
    require_once __DIR__ . '/controllers/MenuController.php';
    $controller = new MenuController($pdo);
    $kind = $path === 'menu/makanan'
        ? 'makanan'
        : ($path === 'menu/minuman' ? 'minuman' : null);
    $controller->menu($kind);
    exit;
}

if ($path === 'cart') {
    require_once __DIR__ . '/controllers/CartController.php';
    $controller = new CartController($pdo);
    $controller->show();
    exit;
}

if ($path === 'cart/add') {
    require_once __DIR__ . '/controllers/CartController.php';
    $controller = new CartController($pdo);
    $controller->add();
    exit;
}

if ($path === 'cart/update') {
    require_once __DIR__ . '/controllers/CartController.php';
    $controller = new CartController($pdo);
    $controller->update();
    exit;
}

if ($path === 'cart/remove') {
    require_once __DIR__ . '/controllers/CartController.php';
    $controller = new CartController($pdo);
    $controller->remove();
    exit;
}

if ($path === 'booking') {
    require_once __DIR__ . '/controllers/BookingController.php';
    $controller = new BookingController($pdo);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->show();
    } else {
        $controller->show();
    }
    exit;
}

if ($path === 'checkout/summary') {
    require_once __DIR__ . '/controllers/BookingController.php';
    $controller = new BookingController($pdo);
    $controller->summary();
    exit;
}

if ($path === 'checkout/confirm') {
    require_once __DIR__ . '/controllers/BookingController.php';
    $controller = new BookingController($pdo);
    $controller->confirm();
    exit;
}

if ($path === 'transactions') {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/TransactionController.php';
    $controller = new TransactionController($pdo);
    $controller->index();
    exit;
}

if (preg_match('#^transactions/detail/([0-9]+)$#', $path, $matches)) {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/TransactionController.php';
    $controller = new TransactionController($pdo);
    $controller->detail((int) $matches[1]);
    exit;
}

if (preg_match('#^transactions/cancel/([0-9]+)$#', $path, $matches)) {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/TransactionController.php';
    $controller = new TransactionController($pdo);
    $controller->cancel((int) $matches[1]);
    exit;
}

if ($path === 'notifications') {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/NotificationController.php';
    $controller = new NotificationController($pdo);
    $controller->index();
    exit;
}

if ($path === 'notifications/read') {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/NotificationController.php';
    $controller = new NotificationController($pdo);
    $controller->markRead();
    exit;
}

if ($path === 'notifications/read-all') {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/NotificationController.php';
    $controller = new NotificationController($pdo);
    $controller->markAllRead();
    exit;
}

if (str_starts_with($path, 'admin/')) {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/controllers/AdminController.php';
    require_once __DIR__ . '/controllers/PaymentController.php';
    require_once __DIR__ . '/controllers/OrderController.php';
    require_once __DIR__ . '/controllers/ReportController.php';
    require_once __DIR__ . '/controllers/BackupController.php';

    $admin = new AdminController($pdo);
    $payment = new PaymentController($pdo);
    $order = new OrderController($pdo);
    $report = new ReportController($pdo);
    $backup = new BackupController($pdo);

    $route = substr($path, 6);
    if ($route === 'dashboard') {
        $admin->dashboard();
    } elseif ($route === 'categories') {
        $admin->categories();
    } elseif ($route === 'menus') {
        $admin->menus();
    } elseif ($route === 'tables') {
        $admin->tables();
    } elseif ($route === 'bookings') {
        $admin->bookings();
    } elseif ($route === 'orders') {
        $order->index();
    } elseif ($route === 'orders/status') {
        $order->updateStatus();
    } elseif ($route === 'orders/edit') {
        $order->editDetail();
    } elseif ($route === 'payments') {
        $payment->index();
    } elseif ($route === 'payments/confirm') {
        $payment->confirm();
    } elseif ($route === 'customers') {
        $admin->customers();
    } elseif ($route === 'settings') {
        $admin->settings();
    } elseif ($route === 'profile') {
        $admin->adminProfile();
    } elseif ($route === 'reports') {
        $report->index();
    } elseif ($route === 'backup') {
        $backup->page();
    } elseif ($route === 'backup/download') {
        $backup->download();
    } else {
        http_response_code(404);
        require __DIR__ . '/views/admin/not_found.php';
    }
    exit;
}

require_once __DIR__ . '/controllers/MenuController.php';
$controller = new MenuController($pdo);
$controller->home();
