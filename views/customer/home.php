<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($settings['cafe_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main>
        <?php if ($flash): ?>
            <div class="container pt-4">
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            </div>
        <?php endif; ?>

        <section class="hero-panel" style="<?= !empty($settings['banner']) ? 'background-image: linear-gradient(rgba(250,246,240,.88),rgba(250,246,240,.88)),url(' . e(BASE_URL . '/' . ltrim($settings['banner'], '/')) . ');background-size:cover;background-position:center;' : '' ?>">
            <div class="container text-center py-5">
                <?php if (!empty($settings['logo'])): ?>
                    <img class="cafe-logo mb-3" src="<?= e(BASE_URL . '/' . ltrim($settings['logo'], '/')) ?>"
                         alt="Logo <?= e($settings['cafe_name']) ?>">
                <?php endif; ?>
                <p class="eyebrow mb-2">KAFE MODERN & NYAMAN</p>
                <h1 class="display-5 fw-bold mb-3"><?= e($settings['cafe_name']) ?></h1>
                <p class="lead mx-auto mb-4"><?= e($settings['description']) ?></p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a class="btn btn-coffee" href="<?= e(BASE_URL) ?>/menu">Lihat Menu</a>
                    <a class="btn btn-outline-coffee" href="<?= e(BASE_URL) ?>/booking">Booking Meja</a>
                </div>
            </div>
        </section>

        <section class="container py-5">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-4 flex-wrap">
                <div>
                    <p class="eyebrow mb-1">Pilihan Menu</p>
                    <h2 class="h3 mb-0">Seluruh Menu</h2>
                </div>
                <a class="btn btn-outline-coffee" href="<?= e(BASE_URL) ?>/menu">Lihat Semua</a>
            </div>

            <?php if ($menus === []): ?>
                <div class="alert alert-light border">Belum ada menu yang dapat ditampilkan.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($menus as $menu): ?>
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <?php require __DIR__ . '/menu_card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="cafe-info-section border-top border-bottom">
            <div class="container py-5">
                <div class="row g-4">
                    <div class="col-md-4">
                        <h2 class="h5">Jam Operasional</h2>
                        <p class="mb-0">09.00–22.00 WIB setiap hari</p>
                    </div>
                    <div class="col-md-4">
                        <h2 class="h5">Area Cafe</h2>
                        <p class="mb-0">Indoor dan Outdoor</p>
                    </div>
                    <div class="col-md-4">
                        <h2 class="h5">Alamat & Kontak</h2>
                        <p class="mb-1"><?= e($settings['address'] ?: '-') ?></p>
                        <p class="mb-0"><?= e($settings['phone'] ?: '-') ?></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>
</body>
</html>
