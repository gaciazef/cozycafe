<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Transaksi - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <a class="btn btn-link px-0 mb-3" href="<?= e(BASE_URL) ?>/transactions">← Kembali ke Riwayat</a>
        <h1 class="h2 mb-4">Detail Transaksi <?= e($transaction['transaction_code']) ?></h1>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <section class="card border-0 shadow-sm p-4 mb-4">
                    <h2 class="h5">Informasi Booking</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Tanggal transaksi</dt>
                        <dd class="col-sm-8"><?= format_tanggal($transaction['created_at'], true) ?> WIB</dd>
                        <dt class="col-sm-4">Tanggal & Jam</dt>
                        <dd class="col-sm-8"><?= format_tanggal($transaction['booking_date']) ?>,
                            <?= format_waktu($transaction['start_time']) ?>–<?= format_waktu($transaction['end_time']) ?> WIB</dd>
                        <dt class="col-sm-4">Meja</dt>
                        <dd class="col-sm-8"><?= e($transaction['table_number']) ?> (<?= e($transaction['area']) ?>)</dd>
                        <dt class="col-sm-4">Jumlah tamu</dt>
                        <dd class="col-sm-8"><?= e($transaction['guest_count']) ?> orang</dd>
                    </dl>
                </section>

                <section class="card border-0 shadow-sm p-4 mb-4">
                    <h2 class="h5">Daftar Pesanan</h2>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between gap-3 border-bottom py-3">
                            <div>
                                <strong><?= e($item['menu_name_snapshot']) ?></strong>
                                <div class="small text-secondary"><?= e($item['quantity']) ?> × <?= format_rupiah($item['price_snapshot']) ?></div>
                                <?php if (!empty($item['note'])): ?>
                                    <div class="small">Catatan: <?= e($item['note']) ?></div>
                                <?php endif; ?>
                            </div>
                            <strong><?= format_rupiah($item['subtotal']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </section>

                <?php if ($changes): ?>
                    <section class="card border-0 shadow-sm p-4 mb-4">
                        <h2 class="h5">Riwayat Perubahan Pesanan</h2>
                        <?php foreach ($changes as $change): ?>
                            <div class="border-bottom py-2">
                                <div><?= e($change['change_description']) ?></div>
                                <div class="small text-secondary">
                                    <?= format_rupiah($change['old_total']) ?> → <?= format_rupiah($change['new_total']) ?>
                                    · <?= format_tanggal($change['created_at'], true) ?> WIB
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <?php if ($cancellation): ?>
                    <section class="card border-0 shadow-sm p-4">
                        <h2 class="h5">Alasan Pembatalan</h2>
                        <p class="mb-1"><?= e($cancellation['reason']) ?></p>
                        <div class="small text-secondary">
                            Dibatal oleh: <?= e($cancellation['canceled_by']) ?>
                            · <?= format_tanggal($cancellation['created_at'], true) ?> WIB
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <aside class="card border-0 shadow-sm p-4 sticky-summary">
                    <h2 class="h5">Status & Pembayaran</h2>
                    <p class="mb-1">Status booking: <strong><?= e($transaction['booking_status']) ?></strong></p>
                    <p class="mb-1">Status pesanan: <strong><?= e($transaction['order_status']) ?></strong></p>
                    <p class="mb-3">Status pembayaran: <strong><?= e($transaction['payment_status']) ?></strong></p>
                    <div class="d-flex justify-content-between border-top pt-3">
                        <span>Total</span>
                        <strong><?= format_rupiah($transaction['total_amount']) ?></strong>
                    </div>

                    <?php if ($canCancel): ?>
                        <form method="post" action="<?= e(BASE_URL) ?>/transactions/cancel/<?= e($transaction['id']) ?>" class="mt-4">
                            <?= csrf_field() ?>
                            <label class="form-label" for="reason">Alasan pembatalan</label>
                            <textarea class="form-control mb-3" id="reason" name="reason" rows="3"
                                      maxlength="500" placeholder="Opsional">Dibatalkan oleh pelanggan</textarea>
                            <button class="btn btn-danger w-100" type="submit"
                                    data-confirm="Batalkan transaksi ini?">
                                Batalkan Transaksi
                            </button>
                        </form>
                    <?php elseif ($transaction['booking_status'] === 'Dikonfirmasi'): ?>
                        <div class="alert alert-light border mt-4 mb-0">
                            Pembatalan tidak tersedia kurang dari 2 jam sebelum jadwal. Silakan hubungi admin.
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>
</body>
</html>
