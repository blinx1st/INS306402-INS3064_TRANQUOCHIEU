<section class="panel">
    <h1 class="page-title" style="text-align:center;">CHÀO MỪNG BẠN ĐẾN VỚI CÂU LẠC BỘ TIN HỌC NTU</h1>
    <div class="login-box">
        <img src="<?= asset_url('Image/Logo.jpg') ?>" alt="Câu lạc bộ tin học NTU">
        <form method="post" action="<?= url_for('Login_64131060', 'Login_64131060') ?>">
            <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
            <div class="mb-3">
                <label class="form-label" for="email">Email/User</label>
                <input class="form-control" id="email" type="email" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="matKhau">Mật khẩu</label>
                <input class="form-control" id="matKhau" type="password" name="matKhau" required>
            </div>
            <div class="toolbar">
                <button class="btn-main" type="submit">Đăng nhập</button>
                <a class="btn-back" href="<?= url_for('ThanhVien_Member_64131060', 'Create') ?>">Đăng ký</a>
            </div>
        </form>
    </div>
</section>
