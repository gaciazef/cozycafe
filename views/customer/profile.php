<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Pelanggan - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-4">Profil Pelanggan</h1>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                                <?= e($flash['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                            </div>
                        <?php endif; ?>
                        <form method="post" action="<?= e(BASE_URL) ?>/customer/profile">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label" for="name">Nama Lengkap</label>
                                <input class="form-control" id="name" name="name" maxlength="100"
                                       value="<?= e($user['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control" id="email" name="email" type="email" maxlength="255"
                                       value="<?= e($user['email']) ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="phone">Nomor Telepon</label>
                                <input class="form-control" id="phone" name="phone" maxlength="30"
                                       value="<?= e($user['phone'] ?? '') ?>">
                            </div>
                            <h2 class="h5 mb-3">Ganti Password (opsional)</h2>
                            <div class="mb-3">
                                <label class="form-label" for="current_password">Password Lama</label>
                                <input class="form-control" id="current_password" name="current_password" type="password"
                                       autocomplete="current-password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="new_password">Password Baru</label>
                                <input class="form-control" id="new_password" name="new_password" type="password"
                                       minlength="8" autocomplete="new-password">
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="new_password_confirmation">Konfirmasi Password Baru</label>
                                <input class="form-control" id="new_password_confirmation"
                                       name="new_password_confirmation" type="password" minlength="8"
                                       autocomplete="new-password">
                            </div>
                            <button class="btn btn-coffee" type="submit">Simpan Profil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
