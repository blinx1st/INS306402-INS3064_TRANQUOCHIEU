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
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/thamkhao2_xampp/public/
```

Trang đăng nhập:

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/thamkhao2_xampp/public/Login_64131060/Login_64131060
```

Nếu Apache chưa bật `mod_rewrite` hoặc `.htaccess` chưa chạy, dùng tạm dạng:

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/thamkhao2_xampp/public/index.php?url=Login_64131060/Login_64131060
```

## Tài khoản mẫu

Mật khẩu đều là `123`.

- Chủ nhiệm: `thai.tt.64cntt@ntu.edu.vn`
- Trợ giảng: `kiet.pt.64cntt@ntu.edu.vn`
- Thành viên: `dung.tdh.64cntt@ntu.edu.vn`

## Gửi email Gmail

Chức năng email dùng SMTP Gmail. Gmail cần App Password, không dùng mật khẩu đăng nhập Gmail thường.
