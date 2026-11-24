<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backup Database - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/admin_header.php'; ?>
    <?php require dirname(__DIR__) . '/layouts/admin_sidebar.php'; ?>
    <main class="admin-main container-fluid px-3 px-lg-4 py-4">
        <h1 class="h3 mb-4">Backup Database</h1>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm p-4" style="max-width: 720px;">
            <h2 class="h5">Unduh Cadangan Database</h2>
            <p class="text-secondary">
                Backup dibuat langsung dari database MySQL melalui PHP PDO.
                Hasil unduhan berupa file SQL yang dapat disimpan sebagai cadangan.
            </p>
            <a class="btn btn-coffee" href="<?= e(BASE_URL) ?>/admin/backup/download">
                Download Backup Database (.sql)
            </a>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
