<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifikasi - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
            <h1 class="h2 mb-0">Notifikasi</h1>
            <?php if ($notifications !== []): ?>
                <form method="post" action="<?= e(BASE_URL) ?>/notifications/read-all">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-coffee" type="submit">Tandai Semua Sudah Dibaca</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <?php if ($notifications === []): ?>
            <div class="card border-0 shadow-sm p-4 text-center">Belum ada notifikasi.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($notifications as $notification): ?>
                    <div class="col-12">
                        <article class="card border-0 shadow-sm <?= $notification['is_read'] ? '' : 'notification-unread' ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-3 flex-wrap">
                                    <div>
                                        <h2 class="h5 mb-1"><?= e($notification['title']) ?></h2>
                                        <p class="mb-1"><?= e($notification['message']) ?></p>
                                        <div class="small text-secondary"><?= format_tanggal($notification['created_at'], true) ?> WIB</div>
                                    </div>
                                    <?php if (!$notification['is_read']): ?>
                                        <span class="badge bg-danger align-self-start">Belum Dibaca</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary align-self-start">Sudah Dibaca</span>
                                    <?php endif; ?>
                                </div>
                                <form method="post" action="<?= e(BASE_URL) ?>/notifications/read" class="mt-3">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="notification_id" value="<?= e($notification['id']) ?>">
                                    <input type="hidden" name="transaction_id" value="<?= e($notification['transaction_id']) ?>">
                                    <button class="btn btn-sm btn-outline-coffee" type="submit">Buka Transaksi</button>
                                </form>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
