<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($kindTitle) ?> - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-end gap-3 mb-4 flex-wrap">
            <div>
                <p class="eyebrow mb-1">Katalog CozyCafe</p>
                <h1 class="h2 mb-0"><?= e($kindTitle) ?></h1>
            </div>
            <div class="d-flex gap-2">
                <a class="btn <?= $kindTitle === 'Menu Makanan' ? 'btn-coffee' : 'btn-outline-coffee' ?>"
                   href="<?= e(BASE_URL) ?>/menu/makanan">Menu Makanan</a>
                <a class="btn <?= $kindTitle === 'Menu Minuman' ? 'btn-coffee' : 'btn-outline-coffee' ?>"
                   href="<?= e(BASE_URL) ?>/menu/minuman">Menu Minuman</a>
            </div>
        </div>

        <form class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4" method="get">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="search">Cari nama menu</label>
                    <input class="form-control" id="search" name="search" value="<?= e($filters['search']) ?>"
                           placeholder="Contoh: Cappuccino">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="category">Kategori</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Semua kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['id']) ?>"
                                    <?= (int) $filters['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="min_price">Harga minimum</label>
                    <input class="form-control" id="min_price" name="min_price" type="number" min="0"
                           value="<?= e($filters['min_price'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="max_price">Harga maksimum</label>
                    <input class="form-control" id="max_price" name="max_price" type="number" min="0"
                           value="<?= e($filters['max_price'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="sort">Urutkan harga</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="">Nama A-Z</option>
                        <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Termurah</option>
                        <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Termahal</option>
                    </select>
                </div>
                <div class="col-md-5 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" id="available_only" name="available_only" type="checkbox"
                               <?= !empty($filters['available_only']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="available_only">Tampilkan menu tersedia saja</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2 justify-content-md-end">
                    <a class="btn btn-outline-secondary" href="<?= e(BASE_URL) ?>/menu<?= $kind === 'makanan' ? '/makanan' : ($kind === 'minuman' ? '/minuman' : '') ?>">Reset</a>
                    <button class="btn btn-coffee" type="submit">Terapkan Filter</button>
                </div>
            </div>
        </form>

        <?php if ($menus === []): ?>
            <div class="alert alert-light border">Tidak ada menu yang sesuai dengan filter.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($menus as $menu): ?>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <?php require __DIR__ . '/menu_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>
</body>
</html>
