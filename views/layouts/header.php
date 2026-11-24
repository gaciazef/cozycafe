<?php
$user = current_user();
$cartCount = (int) ($_SESSION['cart_count'] ?? 0);
$unreadCount = (int) ($_SESSION['unread_notifications'] ?? 0);
?>
<nav class="navbar navbar-expand-lg navbar-cozy sticky-top" aria-label="Navigasi utama">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= e(BASE_URL) ?>/">CozyCafe</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation"
                aria-controls="mainNavigation" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavigation">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/menu/makanan">Menu Makanan</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/menu/minuman">Menu Minuman</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= e(BASE_URL) ?>/cart">
                        Keranjang
                        <?php if ($cartCount > 0): ?>
                            <span class="badge rounded-pill bg-danger"><?= e($cartCount) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?= e(BASE_URL) ?>/booking">Booking Meja</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($user): ?>
                    <a class="nav-link position-relative" href="<?= e(BASE_URL) ?>/notifications"
                       aria-label="Notifikasi">
                        Notifikasi
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge rounded-pill bg-danger"><?= e($unreadCount) ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="btn btn-sm btn-outline-coffee" href="<?= e(BASE_URL) ?>/transactions">Riwayat</a>
                    <a class="btn btn-sm btn-outline-coffee" href="<?= e(BASE_URL) ?>/customer/profile">Profil</a>
                    <a class="btn btn-sm btn-coffee" href="<?= e(BASE_URL) ?>/auth/logout">Keluar</a>
                <?php else: ?>
                    <a class="btn btn-sm btn-coffee" href="<?= e(BASE_URL) ?>/auth/login">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
