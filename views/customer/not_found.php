<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5 text-center">
        <h1>404</h1>
        <p>Halaman pelanggan tidak ditemukan.</p>
        <a class="btn btn-coffee" href="<?= e(BASE_URL) ?>/">Kembali ke Beranda</a>
    </main>
</body>
</html>
