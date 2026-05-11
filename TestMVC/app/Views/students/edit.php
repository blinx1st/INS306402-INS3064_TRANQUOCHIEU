<section class="page-head">
    <div>
        <p class="eyebrow">Update</p>
        <h1>Cap nhat sinh vien</h1>
    </div>
    <a class="button button--muted" href="<?= e(url('students')) ?>">Quay lai</a>
</section>

<?php
$action = url('students/update/' . $student['id']);
$buttonText = 'Cap nhat';
require APP_ROOT . '/app/Views/students/_form.php';
?>

