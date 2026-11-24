<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <h1 class="h2 mb-4">Riwayat Transaksi</h1>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <?php if ($transactions === []): ?>
            <div class="card border-0 shadow-sm p-4 text-center">
                <p class="mb-3">Belum ada transaksi.</p>
                <a class="btn btn-coffee" href="<?= e(BASE_URL) ?>/menu">Lihat Menu</a>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($transactions as $transaction): ?>
                    <div class="col-12">
                        <article class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-3 flex-wrap">
                                    <div>
                                        <div class="text-secondary small">Kode Transaksi</div>
                                        <h2 class="h5 mb-1"><?= e($transaction['transaction_code']) ?></h2>
                                        <div class="small text-secondary"><?= format_tanggal($transaction['created_at'], true) ?> WIB</div>
                                    </div>
                                    <strong><?= format_rupiah($transaction['total_amount']) ?></strong>
                                </div>
                                <dl class="row mt-3 mb-2">
                                    <dt class="col-md-3">Tanggal & Jam Booking</dt>
                                    <dd class="col-md-9"><?= format_tanggal($transaction['booking_date']) ?>,
                                        <?= format_waktu($transaction['start_time']) ?>–<?= format_waktu($transaction['end_time']) ?> WIB</dd>
                                    <dt class="col-md-3">Meja</dt>
                                    <dd class="col-md-9"><?= e($transaction['table_number']) ?> (<?= e($transaction['area']) ?>),
                                        <?= e($transaction['guest_count']) ?> tamu</dd>
                                </dl>
                                <div class="mb-3">
                                    <?php foreach ($transaction['items'] as $item): ?>
                                        <span class="badge text-bg-light border me-1">
                                            <?= e($item['menu_name_snapshot']) ?> × <?= e($item['quantity']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge bg-primary"><?= e($transaction['booking_status']) ?></span>
                                    <span class="badge bg-info text-dark"><?= e($transaction['order_status']) ?></span>
                                    <span class="badge <?= $transaction['payment_status'] === 'Sudah Dibayar' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= e($transaction['payment_status']) ?>
                                    </span>
                                </div>
                                <a class="btn btn-sm btn-outline-coffee mt-3"
                                   href="<?= e(BASE_URL) ?>/transactions/detail/<?= e($transaction['id']) ?>">
                                    Lihat Detail
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-4" aria-label="Paginasi transaksi">
                    <ul class="pagination">
                        <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
                            <li class="page-item <?= $pageNumber === (int) ($_GET['page'] ?? 1) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e(BASE_URL) ?>/transactions?page=<?= $pageNumber ?>">
                                    <?= $pageNumber ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
