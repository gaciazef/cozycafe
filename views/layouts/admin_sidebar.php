<aside class="offcanvas-lg offcanvas-start admin-sidebar" tabindex="-1" id="adminSidebar"
       aria-labelledby="adminSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminSidebarLabel">Navigasi Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body flex-column p-3">
        <nav class="nav nav-pills flex-column">
            <?php
            $adminLinks = [
                'dashboard' => 'Dashboard',
                'categories' => 'Kelola Kategori Menu',
                'menus' => 'Kelola Menu',
                'tables' => 'Kelola Meja',
                'bookings' => 'Kelola Booking',
                'orders' => 'Kelola Pesanan',
                'payments' => 'Kelola Pembayaran',
                'customers' => 'Kelola Pelanggan',
                'reports' => 'Laporan Transaksi',
                'settings' => 'Pengaturan Profil',
                'backup' => 'Backup Database',
            ];
            foreach ($adminLinks as $route => $label):
            ?>
                <a class="nav-link" href="<?= e(BASE_URL) ?>/admin/<?= e($route) ?>">
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
            <a class="nav-link text-danger" href="<?= e(BASE_URL) ?>/auth/logout">Keluar</a>
        </nav>
    </div>
</aside>
