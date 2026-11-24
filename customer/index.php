<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';

$route = $_GET['route'] ?? '';

if ($route === 'profile') {
    require_customer();
    redirect('customer/profile');
}

if ($route === 'cart') {
    require_customer();
    redirect('cart');
}

if ($route === 'booking') {
    require_customer();
    redirect('booking');
}

if ($route === 'transactions') {
    require_customer();
    redirect('transactions');
}

if ($route === 'notifications') {
    require_customer();
    redirect('notifications');
}

redirect('menu');
