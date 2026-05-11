<section class="panel">
    <h1 class="page-title"><?= h($data['title'] ?? 'Giới thiệu về câu lạc bộ') ?></h1>
    <p style="font-size:18px;line-height:1.8;">CLB Tin học NTU là nơi sinh viên yêu thích công nghệ cùng học tập, chia sẻ kiến thức và tổ chức các hoạt động chuyên môn.</p>
    <p style="font-size:18px;line-height:1.8;">Bản PHP này được chuyển từ dự án ASP.NET MVC gốc để chạy trên XAMPP, giữ các luồng quản lý chính: thành viên, sự kiện, nhóm học tập, bài đăng, điểm danh và email.</p>
    <div class="toolbar">
        <a class="btn-back" href="<?= url_for('TrangChu_64131060', $data['homeAction'] ?? 'TrangChu_64131060') ?>">QUAY VỀ</a>
    </div>
</section>
