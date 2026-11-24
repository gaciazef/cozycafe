<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Meja - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <h1 class="h2 mb-2">Booking Meja</h1>
        <p class="text-secondary">Tanggal booking: <strong><?= format_tanggal($now->format('Y-m-d')) ?> WIB</strong></p>
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL) ?>/booking" class="card border-0 shadow-sm p-4 mb-4">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="guest_count">Jumlah Tamu</label>
                    <input class="form-control" id="guest_count" name="guest_count" type="number"
                           min="1" max="20" value="<?= e($guestCount) ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="slot">Slot Waktu</label>
                    <select class="form-select" id="slot" name="slot" required>
                        <option value="">Pilih slot</option>
                        <?php foreach ($slots as $slot): ?>
                            <option value="<?= e($slot['start']) ?>" <?= $selectedSlot === $slot['start'] ? 'selected' : '' ?>>
                                <?= e($slot['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-coffee w-100" type="submit">Cari Meja Tersedia</button>
                </div>
            </div>
        </form>

        <?php if ($selectedSlot !== '' && $slotParts === null): ?>
            <div class="alert alert-danger">Slot waktu tidak valid atau sudah lewat.</div>
        <?php elseif ($selectedSlot !== ''): ?>
            <h2 class="h5 mb-3">Meja Tersedia</h2>
            <?php if ($availableTables === []): ?>
                <div class="alert alert-light border">Tidak ada meja yang tersedia untuk jumlah tamu dan waktu tersebut.</div>
            <?php else: ?>
                <form method="post" action="<?= e(BASE_URL) ?>/checkout/summary">
                    <?= csrf_field() ?>
                    <input type="hidden" name="guest_count" value="<?= e($guestCount) ?>">
                    <input type="hidden" name="start_time" value="<?= e($slotParts['start']) ?>">
                    <div class="row g-3">
                        <?php foreach ($availableTables as $table): ?>
                            <div class="col-sm-6 col-lg-4">
                                <label class="card border shadow-sm h-100 table-card">
                                    <div class="card-body">
                                        <input class="form-check-input float-end" type="radio" name="table_id"
                                               value="<?= e($table['id']) ?>" required>
                                        <h3 class="h5"><?= e($table['table_number']) ?></h3>
                                        <div>Area: <?= e($table['area']) ?></div>
                                        <div>Kapasitas: <?= e($table['capacity']) ?> tamu</div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn btn-coffee mt-4" type="submit">Lihat Ringkasan</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
