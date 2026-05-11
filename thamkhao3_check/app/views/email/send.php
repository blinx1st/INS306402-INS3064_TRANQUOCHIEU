<section class="panel">
    <h1 class="page-title"><?= h($data['title'] ?? 'Gửi email') ?></h1>
    <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
    <form method="post" action="">
        <div class="form-grid">
            <div class="form-field"><label>Email gửi</label><input class="form-control" type="email" name="From" required></div>
            <div class="form-field"><label>App Password Gmail</label><input class="form-control" type="password" name="Password" required></div>
            <div class="form-field"><label>Email nhận</label><input class="form-control" type="email" name="To" required></div>
            <div class="form-field"><label>Tiêu đề</label><input class="form-control" type="text" name="Subject" required></div>
        </div>
        <div class="form-field" style="margin-top:16px;"><label>Nội dung</label><textarea class="form-control" name="Body" required></textarea></div>
        <p class="text-muted" style="margin-top:10px;">Gmail cần App Password, không dùng mật khẩu tài khoản thường.</p>
        <div class="toolbar" style="margin-top:18px;"><button class="btn-main" type="submit">GỬI MAIL</button></div>
    </form>
</section>
