<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ringkasan Checkout - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <h1 class="h2 mb-4">Ringkasan Pesanan</h1>
        <div class="row g-4">
            <div class="col-lg-7">
                <section class="card border-0 shadow-sm p-4 mb-4">
                    <h2 class="h5">Informasi Booking</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8"><?= format_tanggal($checkout['booking_date']) ?></dd>
                        <dt class="col-sm-4">Jam</dt><dd class="col-sm-8"><?= format_waktu($checkout['start_time']) ?>–<?= format_waktu($checkout['end_time']) ?> WIB</dd>
                        <dt class="col-sm-4">Meja</dt><dd class="col-sm-8"><?= e($checkout['table_number']) ?> (<?= e($checkout['area']) ?>)</dd>
                        <dt class="col-sm-4">Jumlah tamu</dt><dd class="col-sm-8"><?= e($checkout['guest_count']) ?> orang</dd>
                        <dt class="col-sm-4">Pembayaran</dt><dd class="col-sm-8">Bayar langsung di kasir cafe</dd>
                    </dl>
                </section>
                <section class="card border-0 shadow-sm p-4">
                    <h2 class="h5">Menu Pesanan</h2>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between gap-3 border-bottom py-3">
                            <div>
                                <strong><?= e($item['name']) ?></strong>
                                <div class="small text-secondary"><?= e($item['quantity']) ?> × <?= format_rupiah($item['price']) ?></div>
                                <?php if (!empty($item['note'])): ?>
                                    <div class="small">Catatan: <?= e($item['note']) ?></div>
                                <?php endif; ?>
                            </div>
                            <strong><?= format_rupiah($item['subtotal']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </section>
            </div>
            <div class="col-lg-5">
                <aside class="card border-0 shadow-sm p-4 sticky-summary">
                    <div class="d-flex justify-content-between">
                        <span>Total Pembayaran</span>
                        <strong><?= format_rupiah($total) ?></strong>
                    </div>
                    <form method="post" action="<?= e(BASE_URL) ?>/checkout/confirm" class="mt-4">
                        <?= csrf_field() ?>
                        <button class="btn btn-coffee w-100" type="submit">Konfirmasi Pesanan</button>
                    </form>
                    <a class="btn btn-outline-secondary w-100 mt-2" href="<?= e(BASE_URL) ?>/booking">Kembali</a>
                </aside>
            </div>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
