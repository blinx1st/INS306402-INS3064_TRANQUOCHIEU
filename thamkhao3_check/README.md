## Yêu cầu

- XAMPP có Apache, MySQL/MariaDB và PHP 8.x.
- Bật extension `pdo_mysql` và `openssl` trong PHP nếu chưa bật.

## Cài database

1. Mở XAMPP Control Panel.
2. Start `Apache` và `MySQL`.
3. Mở `http://localhost/phpmyadmin`.
4. Import file:

```text
database/clbtinhoc_64131060_mysql.sql
```

Database mặc định là `clbtinhoc_64131060`.

Nếu MySQL của bạn không dùng `root` mật khẩu rỗng, sửa file:

```text
app/config/database.php
```

## Chạy web

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/thamkhao3_check/public/
```

Trang đăng nhập:

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/thamkhao3_check/public/Login_64131060/Login_64131060
```

Nếu Apache chưa bật `mod_rewrite` hoặc `.htaccess` chưa chạy, dùng tạm dạng:

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/thamkhao3_check/public/index.php?url=Login_64131060/Login_64131060
```

## Tài khoản mẫu

Mật khẩu đều là `123`.

- Chủ nhiệm: `quochieuu@vnuis.edu.vn`
- Trợ giảng: `trang@vnuis.edu.vn`
- Thành viên: `hongphuong@vnuis.edu.vn`

## Gửi email Gmail

Chức năng email dùng SMTP Gmail. Gmail cần App Password, không dùng mật khẩu đăng nhập Gmail thường.

## Cách giải thích source code khi bảo vệ

Nên trình bày theo luồng MVC:

1. `public/index.php` là cửa vào duy nhất của website, khởi tạo session và gọi Router.
2. `app/core/Router.php` đọc URL dạng `Controller/Action/Params`.
3. Controller kiểm tra quyền, nhận request và gọi Repository.
4. `app/core/Repository.php` dùng PDO để truy vấn database và xử lý nghiệp vụ.
5. `app/core/Validator.php` kiểm tra dữ liệu ở backend trước khi ghi database.
6. View trong `app/views` chỉ hiển thị HTML.
7. `app/controllers/Api_64131060Controller.php` trả JSON cho các thao tác dùng fetch/AJAX.

Tài liệu ôn bảo vệ nằm trong thư mục `docs`:

- `Giai_thich_kien_truc_MVC.md`
- `Luong_nghiep_vu_chinh.md`
- Các file `.docx` tương ứng để mở bằng Word.
