<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(asset('assets/style.css')) ?>">
</head>
<body>
    <header class="topbar">
        <div class="container topbar__inner">
            <a class="brand" href="<?= e(url('students')) ?>"><?= e(APP_NAME) ?></a>
            <nav class="nav">
                <a href="<?= e(url('students')) ?>">Sinh vien</a>
                <a href="<?= e(url('students/create')) ?>">Them moi</a>
            </nav>
        </div>
    </header>

    <main class="container main">
        <?= $content ?>
    </main>
</body>
</html>

