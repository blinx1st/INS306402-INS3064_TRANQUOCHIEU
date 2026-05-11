<section class="page-head">
    <div>
        <p class="eyebrow">Create</p>
        <h1>Them sinh vien</h1>
    </div>
    <a class="button button--muted" href="<?= e(url('students')) ?>">Quay lai</a>
</section>

<?php
$action = url('students/store');
$student = $old;
$buttonText = 'Luu sinh vien';
require APP_ROOT . '/app/Views/students/_form.php';
?>

