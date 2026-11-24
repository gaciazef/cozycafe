<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/admin_header.php'; ?>
    <?php require dirname(__DIR__) . '/layouts/admin_sidebar.php'; ?>
    <main class="admin-main container-fluid p-4">
        <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
        <h1 class="h3 mb-4">Dashboard Admin</h1>
        <div class="row g-3">
            <?php
            $cards = [
                'Pelanggan' => $stats['customers'],
                'Menu' => $stats['menus'],
                'Booking' => $stats['bookings'],
                'Pesanan' => $stats['orders'],
                'Pendapatan' => format_rupiah($stats['revenue']),
            ];
            foreach ($cards as $label => $value): ?>
                <div class="col-sm-6 col-xl">
                    <div class="card border-0 shadow-sm p-3"><div class="small text-secondary"><?= e($label) ?></div><strong class="h4"><?= e($value) ?></strong></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row g-4 mt-2">
            <div class="col-lg-6"><div class="card border-0 shadow-sm p-4"><h2 class="h5">Transaksi 14 Hari Terakhir</h2><canvas id="dailyChart"></canvas></div></div>
            <div class="col-lg-6"><div class="card border-0 shadow-sm p-4"><h2 class="h5">Transaksi Bulanan</h2><canvas id="monthlyChart"></canvas></div></div>
        </div>
        <div class="row g-4 mt-1">
            <div class="col-lg-6"><div class="card border-0 shadow-sm p-4"><h2 class="h5">Booking Terbaru</h2>
                <?php foreach ($latestBookings as $booking): ?><div class="border-bottom py-2"><strong><?= e($booking['transaction_code']) ?></strong> · Meja <?= e($booking['table_number']) ?> · <?= e($booking['status']) ?></div><?php endforeach; ?>
            </div></div>
            <div class="col-lg-6"><div class="card border-0 shadow-sm p-4"><h2 class="h5">Pesanan Terbaru</h2>
                <?php foreach ($latestOrders as $order): ?><div class="border-bottom py-2"><strong><?= e($order['transaction_code']) ?></strong> · <?= e($order['order_status']) ?> · <?= format_rupiah($order['subtotal']) ?></div><?php endforeach; ?>
            </div></div>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    new Chart(document.getElementById('dailyChart'), {type:'bar',data:{labels:<?= json_encode(array_column($daily,'label'), JSON_UNESCAPED_UNICODE) ?>,datasets:[{label:'Transaksi',data:<?= json_encode(array_map('intval',array_column($daily,'total'))) ?>,backgroundColor:'#6f4e37'}]}});
    new Chart(document.getElementById('monthlyChart'), {type:'line',data:{labels:<?= json_encode(array_column($monthly,'label'), JSON_UNESCAPED_UNICODE) ?>,datasets:[{label:'Transaksi',data:<?= json_encode(array_map('intval',array_column($monthly,'total'))) ?>,borderColor:'#6f4e37',tension:.3}]}});
    </script>
</body>
</html>
