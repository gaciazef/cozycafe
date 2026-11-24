<article class="card h-100 menu-card border-0 shadow-sm">
    <?php if (!empty($menu['image'])): ?>
        <img class="card-img-top menu-image" src="<?= e(BASE_URL . '/' . ltrim($menu['image'], '/')) ?>"
             alt="<?= e($menu['name']) ?>">
    <?php else: ?>
        <div class="menu-image menu-image-placeholder d-flex align-items-center justify-content-center"
             aria-label="Belum ada foto">
            <span class="text-secondary">Foto belum tersedia</span>
        </div>
    <?php endif; ?>
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <span class="badge text-bg-light border"><?= e($menu['category_name']) ?></span>
            <?php if ($menu['availability_status'] === 'Tersedia'): ?>
                <span class="badge bg-success">Tersedia</span>
            <?php else: ?>
                <span class="badge bg-secondary">Tidak Tersedia</span>
            <?php endif; ?>
        </div>
        <h2 class="h5 mt-3"><?= e($menu['name']) ?></h2>
        <p class="text-secondary small flex-grow-1"><?= e($menu['description'] ?? '') ?></p>
        <div class="d-flex align-items-center justify-content-between gap-2">
            <strong class="text-coffee"><?= format_rupiah($menu['price']) ?></strong>
            <?php if ($menu['availability_status'] === 'Tersedia'): ?>
                <?php if (is_logged_in()): ?>
                    <form method="post" action="<?= e(BASE_URL) ?>/cart/add">
                        <?= csrf_field() ?>
                        <input type="hidden" name="menu_id" value="<?= e($menu['id']) ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-sm btn-coffee" type="submit">Tambah ke Keranjang</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-sm btn-coffee" href="<?= e(BASE_URL) ?>/auth/login">
                        Masuk untuk Memesan
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn btn-sm btn-secondary" type="button" disabled>Tidak Tersedia</button>
            <?php endif; ?>
        </div>
    </div>
</article>
