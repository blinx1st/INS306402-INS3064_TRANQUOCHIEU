<form class="panel form" action="<?= e($action) ?>" method="post">
    <div class="field">
        <label for="name">Ho ten</label>
        <input id="name" name="name" value="<?= e($student['name'] ?? '') ?>" autocomplete="name">
        <?php if (isset($errors['name'])): ?>
            <span class="error"><?= e($errors['name']) ?></span>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= e($student['email'] ?? '') ?>" autocomplete="email">
        <?php if (isset($errors['email'])): ?>
            <span class="error"><?= e($errors['email']) ?></span>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="phone">Dien thoai</label>
        <input id="phone" name="phone" value="<?= e($student['phone'] ?? '') ?>" autocomplete="tel">
    </div>

    <div class="field">
        <label for="major">Chuyen nganh</label>
        <input id="major" name="major" value="<?= e($student['major'] ?? '') ?>">
        <?php if (isset($errors['major'])): ?>
            <span class="error"><?= e($errors['major']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button class="button" type="submit"><?= e($buttonText) ?></button>
        <a class="button button--muted" href="<?= e(url('students')) ?>">Huy</a>
    </div>
</form>

