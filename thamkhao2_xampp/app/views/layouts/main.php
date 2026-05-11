<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($data['title'] ?? 'CLB Tin Học NTU') ?></title>
    <link rel="stylesheet" href="<?= asset_url('Content/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('Content/Site.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('Content/StyleAdmin.css') ?>">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; color: #16213e; }
        a { text-decoration: none; }
        .topbar { background: #063b87; color: #fff; position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 10px rgba(0,0,0,.15); }
        .topbar-inner { max-width: 1200px; margin: 0 auto; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { color: #fff; font-weight: 800; display: flex; align-items: center; gap: 10px; font-size: 20px; }
        .brand img { width: 54px; height: 54px; border-radius: 50%; object-fit: cover; }
        .navlinks { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; justify-content: flex-end; }
        .navlinks a { color: #fff; font-weight: 700; font-size: 14px; }
        .navlinks a:hover { color: #b9f15c; }
        .page { max-width: 1200px; margin: 28px auto; padding: 0 18px; min-height: 60vh; }
        .panel { background: #fff; border: 1px solid #dce4f2; border-radius: 8px; padding: 22px; box-shadow: 0 6px 18px rgba(15,42,80,.08); }
        .page-title { font-weight: 800; color: #063b87; margin-bottom: 18px; }
        .toolbar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .btn-main, .btn-back, .btn-danger-soft { border: 0; border-radius: 6px; padding: 9px 14px; font-weight: 700; display: inline-block; }
        .btn-main { background: #0d6efd; color: #fff; }
        .btn-back { background: #6c757d; color: #fff; }
        .btn-danger-soft { background: #dc3545; color: #fff; }
        td { vertical-align: middle; }
        .thumb { width: 110px; max-height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
        .form-field label { font-weight: 700; margin-bottom: 6px; display: block; }
        .form-field textarea { min-height: 120px; }
        .footer { background: #063b87; color: #fff; margin-top: 42px; padding: 22px; text-align: center; }
        .search-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; margin-bottom: 18px; }
        .login-box { max-width: 980px; margin: 30px auto; display: grid; grid-template-columns: minmax(260px, 1fr) minmax(260px, 1fr); gap: 24px; align-items: center; }
        .login-box img { width: 100%; border-radius: 8px; }
        @media (max-width: 720px) { .topbar-inner, .navlinks { align-items: flex-start; justify-content: flex-start; } .topbar-inner { flex-direction: column; } .login-box { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="<?= url_for('TrangChu_64131060', current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : (current_role() === 'TV' ? 'MemberPage_64131060' : 'TrangChu_64131060'))) ?>">
            <img src="<?= asset_url('Image/Logo_Empty.png') ?>" alt="Logo">
            <span>INFOTECH CLUB NTU</span>
        </a>
        <nav class="navlinks">
            <?php if (current_role() === 'TVCN'): ?>
                <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_AdminPage_64131060') ?>">GIỚI THIỆU</a>
                <a href="<?= url_for('BaiDang_Admin_64131060', 'BaiDang_Admin_64131060') ?>">TIN TỨC</a>
                <a href="<?= url_for('SuKien_Admin_64131060', 'TimKiemSuKien_Admin_64131060') ?>">SỰ KIỆN</a>
                <a href="<?= url_for('ThanhVien_Admin_64131060', 'TimKiemTV_Admin_64131060') ?>">THÀNH VIÊN</a>
                <a href="<?= url_for('DiemDanh_Admin_64131060', 'Create') ?>">ĐIỂM DANH</a>
                <a href="<?= url_for('Email_64131060', 'SendMail_Admin_64131060') ?>">MAIL</a>
                <a href="<?= url_for('ThanhVien_Admin_64131060', 'Admin_Page_64131060') ?>">TRANG CÁ NHÂN</a>
                <a href="<?= url_for('Login_64131060', 'Logout_64131060') ?>">ĐĂNG XUẤT</a>
            <?php elseif (current_role() === 'TVTG'): ?>
                <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_AssitantPage_64131060') ?>">GIỚI THIỆU</a>
                <a href="<?= url_for('BaiDang_Assitant_64131060', 'BaiDang_Assitant_64131060') ?>">TIN TỨC</a>
                <a href="<?= url_for('SuKien_Assitant_64131060', 'TimKiemSuKien_Assitant_64131060') ?>">SỰ KIỆN</a>
                <a href="<?= url_for('ThanhVien_Assitant_64131060', 'TimKiemTV_Assitant_64131060') ?>">THÀNH VIÊN</a>
                <a href="<?= url_for('DiemDanh_Assitant_64131060', 'Create') ?>">ĐIỂM DANH</a>
                <a href="<?= url_for('Email_64131060', 'SendMail_Asstant_64131060') ?>">MAIL</a>
                <a href="<?= url_for('ThanhVien_Assitant_64131060', 'Assitant_Page_64131060') ?>">TRANG CÁ NHÂN</a>
                <a href="<?= url_for('Login_64131060', 'Logout_64131060') ?>">ĐĂNG XUẤT</a>
            <?php elseif (current_role() === 'TV'): ?>
                <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_MemberPage_64131060') ?>">GIỚI THIỆU</a>
                <a href="<?= url_for('BaiDang_Member_64131060', 'BaiDang_Member_64131060') ?>">TIN TỨC</a>
                <a href="<?= url_for('SuKien_Member_64131060', 'TimKiemSuKien_Member_64131060') ?>">SỰ KIỆN</a>
                <a href="<?= url_for('DiemDanh_Member_64131060', 'Create') ?>">ĐIỂM DANH</a>
                <a href="<?= url_for('Email_64131060', 'SendMail_Member_64131060') ?>">MAIL</a>
                <a href="<?= url_for('ThanhVien_Member_64131060', 'Member_Page_64131060') ?>">TRANG CÁ NHÂN</a>
                <a href="<?= url_for('Login_64131060', 'Logout_64131060') ?>">ĐĂNG XUẤT</a>
            <?php else: ?>
                <a href="<?= url_for('TrangChu_64131060', 'TrangChu_64131060') ?>">TRANG CHỦ</a>
                <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_64131060') ?>">GIỚI THIỆU</a>
                <a href="<?= url_for('BaiDang_64131060', 'BaiDang_64131060') ?>">TIN TỨC</a>
                <a href="<?= url_for('SuKien_64131060', 'TimKiemSuKien_64131060') ?>">SỰ KIỆN</a>
                <a href="<?= url_for('Login_64131060', 'Login_64131060') ?>">ĐĂNG NHẬP</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="page"><?= $content ?></main>
<footer class="footer">CLB TIN HỌC KHOA CNTT - NTU | Copyright &copy; TuanKiet_64131060 - <?= date('Y') ?></footer>
<script src="<?= asset_url('Scripts/jquery-3.4.1.min.js') ?>"></script>
<script src="<?= asset_url('Scripts/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
