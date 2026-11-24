<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Transaksi - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/admin_header.php'; ?>
    <?php require dirname(__DIR__) . '/layouts/admin_sidebar.php'; ?>
    <main class="admin-main container-fluid px-3 px-lg-4 py-4">
        <h1 class="h3 mb-4">Laporan Transaksi</h1>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <form class="card border-0 shadow-sm p-3 mb-4" method="get">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                    <input class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                           type="date" value="<?= e($tanggalMulai) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="tanggal_akhir">Tanggal Akhir</label>
                    <input class="form-control" id="tanggal_akhir" name="tanggal_akhir"
                           type="date" value="<?= e($tanggalAkhir) ?>" required>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-coffee w-100" type="submit">Tampilkan Laporan</button>
                </div>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4">
                    <div class="small text-secondary">Total Transaksi</div>
                    <strong class="h3 mb-0"><?= e($summary['total_transactions']) ?></strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4">
                    <div class="small text-secondary">Total Pendapatan</div>
                    <strong class="h3 mb-0"><?= format_rupiah($summary['total_revenue']) ?></strong>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Booking</th>
                            <th>Pesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($transactions === []): ?>
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">
                                    Tidak ada transaksi pada rentang tanggal tersebut.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><strong><?= e($transaction['transaction_code']) ?></strong></td>
                                    <td><?= format_tanggal($transaction['created_at'], true) ?> WIB</td>
                                    <td><?= e($transaction['customer_name']) ?></td>
                                    <td>
                                        <?= e($transaction['table_number']) ?>
                                        (<?= e($transaction['area']) ?>)
                                    </td>
                                    <td><?= format_rupiah($transaction['total_amount']) ?></td>
                                    <td><?= e($transaction['payment_status']) ?></td>
                                    <td><?= e($transaction['booking_status']) ?></td>
                                    <td><?= e($transaction['order_status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
