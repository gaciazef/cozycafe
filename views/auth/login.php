<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - CozyCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <?php require dirname(__DIR__) . '/layouts/header.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-4 text-center">Masuk ke CozyCafe</h1>
                        <?php if ($flash): ?>
                            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                                <?= e($flash['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                            </div>
                        <?php endif; ?>
                        <form method="post" action="<?= e(BASE_URL) ?>/auth/login">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control" id="email" name="email" type="email" maxlength="255"
                                       autocomplete="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control" id="password" name="password" type="password"
                                       autocomplete="current-password" required>
                            </div>
                            <button class="btn btn-coffee w-100" type="submit">Masuk</button>
                        </form>
                        <p class="small mt-3 mb-0">
                            Lupa password? Silakan hubungi admin cafe secara langsung.
                        </p>
                        <hr>
                        <p class="text-center mb-0">
                            Belum punya akun?
                            <a href="<?= e(BASE_URL) ?>/auth/register">Daftar sebagai pelanggan</a>
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
