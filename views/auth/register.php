<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Pelanggan - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-4 text-center">Registrasi Pelanggan</h1>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                                <?= e($flash['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                            </div>
                        <?php endif; ?>
                        <form method="post" action="<?= e(BASE_URL) ?>/auth/register">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label" for="name">Nama Lengkap</label>
                                <input class="form-control" id="name" name="name" maxlength="100" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control" id="email" name="email" type="email" maxlength="255"
                                       autocomplete="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="phone">Nomor Telepon</label>
                                <input class="form-control" id="phone" name="phone" maxlength="30"
                                       autocomplete="tel">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control" id="password" name="password" type="password"
                                       minlength="8" autocomplete="new-password" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation"
                                       type="password" minlength="8" autocomplete="new-password" required>
                            </div>
                            <button class="btn btn-coffee w-100" type="submit">Daftar</button>
                        </form>
                        <p class="text-center mt-3 mb-0">
                            Sudah punya akun?
                            <a href="<?= e(BASE_URL) ?>/auth/login">Masuk</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
