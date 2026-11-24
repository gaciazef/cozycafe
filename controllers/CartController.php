<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/Cart.php';

final class CartController
{
    private Cart $cart;

    public function __construct(private PDO $pdo)
    {
        $this->cart = new Cart($pdo);
    }

    public function add(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan keranjang tidak valid.');
            redirect('menu');
        }

        [$success, $message] = $this->cart->add(
            (int) current_user()['id'],
            (int) ($_POST['menu_id'] ?? 0),
            (int) ($_POST['quantity'] ?? 1),
            trim((string) ($_POST['note'] ?? ''))
        );
        set_flash($success ? 'success' : 'danger', $message);
        $_SESSION['cart_count'] = $this->cart->count((int) current_user()['id']);
        redirect($_SERVER['HTTP_REFERER'] ?? 'menu');
    }

    public function update(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan keranjang tidak valid.');
            redirect('cart');
        }

        [$success, $message] = $this->cart->update(
            (int) current_user()['id'],
            (int) ($_POST['item_id'] ?? 0),
            (int) ($_POST['quantity'] ?? 1),
            trim((string) ($_POST['note'] ?? ''))
        );
        set_flash($success ? 'success' : 'danger', $message);
        $_SESSION['cart_count'] = $this->cart->count((int) current_user()['id']);
        redirect('cart');
    }

    public function remove(): void
    {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            set_flash('danger', 'Permintaan keranjang tidak valid.');
            redirect('cart');
        }

        $this->cart->remove(
            (int) current_user()['id'],
            (int) ($_POST['item_id'] ?? 0)
        );
        set_flash('success', 'Item berhasil dihapus dari keranjang.');
        $_SESSION['cart_count'] = $this->cart->count((int) current_user()['id']);
        redirect('cart');
    }

    public function show(): void
    {
        require_customer();
        $items = $this->cart->items((int) current_user()['id']);
        $total = $this->cart->total($items);
        $_SESSION['cart_count'] = $this->cart->count((int) current_user()['id']);
        $flash = get_flash();
        require dirname(__DIR__) . '/views/customer/cart.php';
    }
}
