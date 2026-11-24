<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Berhasil - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5 text-center">
        <div class="card border-0 shadow-sm mx-auto p-5" style="max-width: 650px;">
            <div class="display-6 text-success mb-3">✓</div>
            <h1 class="h3">Pesanan Berhasil Dikonfirmasi</h1>
            <p class="mb-2">Kode transaksi Anda:</p>
            <strong class="h4"><?= e($transactionCode) ?></strong>
            <p class="text-secondary mt-3">Pembayaran dapat dilakukan langsung di kasir cafe.</p>
            <a class="btn btn-coffee mt-3" href="<?= e(BASE_URL) ?>/menu">Kembali ke Menu</a>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
</body>
</html>
