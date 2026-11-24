<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <h1 class="h2 mb-4">Keranjang Belanja</h1>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <?php if ($items === []): ?>
            <div class="card border-0 shadow-sm p-4 text-center">
                <p class="mb-3">Keranjang Anda masih kosong.</p>
                <a class="btn btn-coffee" href="<?= e(BASE_URL) ?>/menu">Pilih Menu</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php foreach ($items as $item): ?>
                        <article class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-3 flex-wrap">
                                    <div>
                                        <h2 class="h5 mb-1"><?= e($item['name']) ?></h2>
                                        <div class="text-secondary"><?= format_rupiah($item['price']) ?></div>
                                    </div>
                                    <strong><?= format_rupiah($item['subtotal']) ?></strong>
                                </div>
                                <form class="row g-2 mt-3" method="post" action="<?= e(BASE_URL) ?>/cart/update">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= e($item['id']) ?>">
                                    <div class="col-sm-3">
                                        <label class="form-label" for="quantity-<?= e($item['id']) ?>">Jumlah</label>
                                        <input class="form-control" id="quantity-<?= e($item['id']) ?>"
                                               name="quantity" type="number" min="1" max="99"
                                               value="<?= e($item['quantity']) ?>" required>
                                    </div>
                                    <div class="col-sm-7">
                                        <label class="form-label" for="note-<?= e($item['id']) ?>">Catatan</label>
                                        <input class="form-control" id="note-<?= e($item['id']) ?>"
                                               name="note" maxlength="500" value="<?= e($item['note'] ?? '') ?>"
                                               placeholder="Contoh: Less sugar">
                                    </div>
                                    <div class="col-sm-2 d-flex align-items-end">
                                        <button class="btn btn-outline-coffee w-100" type="submit">Simpan</button>
                                    </div>
                                </form>
                                <form method="post" action="<?= e(BASE_URL) ?>/cart/remove" class="mt-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= e($item['id']) ?>">
                                    <button class="btn btn-link text-danger px-0" type="submit"
                                            data-confirm="Hapus item ini dari keranjang?">Hapus</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-4">
                    <aside class="card border-0 shadow-sm p-4 sticky-summary">
                        <h2 class="h5">Ringkasan</h2>
                        <div class="d-flex justify-content-between mt-3">
                            <span>Total</span>
                            <strong><?= format_rupiah($total) ?></strong>
                        </div>
                        <a class="btn btn-coffee w-100 mt-4" href="<?= e(BASE_URL) ?>/booking">Lanjut Booking Meja</a>
                    </aside>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>
</body>
</html>
