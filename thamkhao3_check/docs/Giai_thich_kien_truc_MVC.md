# Giải Thích Kiến Trúc MVC Của Dự Án

Tài liệu này dùng để ôn bảo vệ cuối kỳ môn Lập trình Website 2. Dự án được viết bằng PHP thuần, chạy trên XAMPP và tổ chức theo mô hình MVC thủ công.

## 1. Luồng chạy tổng quát

Khi người dùng mở website, request đi vào file `public/index.php`.

File này làm 4 việc chính:

- Mở `session_start()` để lưu trạng thái đăng nhập.
- Khai báo đường dẫn gốc `ROOT_PATH`, `APP_PATH`, `PUBLIC_PATH`.
- Nạp các file lõi như helper, database, controller, repository, validator, router.
- Gọi `(new Router())->dispatch()` để xử lý URL.

Có thể giải thích ngắn gọn: `index.php` là cửa vào duy nhất của hệ thống.

## 2. Router

File `app/core/Router.php` đọc URL theo dạng:

```text
Controller/Action/Params
```

Ví dụ:

```text
SuKien_Admin_64131060/Edit/SK001
```

Router sẽ:

- Lấy tên controller là `SuKien_Admin_64131060`.
- Lấy action là `Edit`.
- Phần còn lại là params truyền vào hàm.
- Nạp file controller tương ứng trong `app/controllers`.
- Tạo object controller và gọi action.

Nếu controller hoặc action không tồn tại, Router đưa về trang chủ hoặc báo lỗi.

## 3. Controller

File `app/core/Controller.php` là base controller. Các controller khác kế thừa file này để dùng lại các hàm chung.

Controller chịu trách nhiệm:

- Render view.
- Kiểm tra đăng nhập.
- Kiểm tra quyền theo role.
- Trả JSON cho API.
- Lấy cấu hình CRUD từ `app/config/resources.php`.
- Gọi Repository để lấy hoặc ghi dữ liệu.

Các role chính:

- `TVCN`: Admin hoặc Phòng CTSV.
- `TVTG`: Ban tổ chức hoặc trợ giảng.
- `TV`: Sinh viên hoặc thành viên.

## 4. CrudSupport

File `app/core/CrudSupport.php` chứa các hàm CRUD dùng chung:

- Hiển thị danh sách.
- Hiển thị chi tiết.
- Hiển thị form thêm/sửa.
- Xử lý tạo mới.
- Xử lý cập nhật.
- Xử lý xóa.
- Thu thập dữ liệu từ form.
- Xử lý upload ảnh.

Việc tách file này giúp `Controller.php` ngắn hơn và dễ giải thích hơn. Khi bảo vệ có thể nói: phần CRUD lặp lại được gom vào trait để tránh viết lại nhiều lần.

## 5. Resources Config

File `app/config/resources.php` mô tả cấu hình CRUD cho từng bảng.

Mỗi resource thường có:

- `table`: tên bảng trong database.
- `pk`: khóa chính.
- `title`: tiêu đề hiển thị.
- `fields`: các field dùng cho form.
- `list`: các cột hiển thị ở danh sách.

Ví dụ resource `SuKien` mô tả các trường như mã sự kiện, tên sự kiện, CLB tổ chức, loại sự kiện, học kỳ, năm học, sức chứa và thời gian mở/đóng QR.

Lợi ích: muốn đổi label hoặc field form thì chỉnh cấu hình, không phải sửa nhiều view.

## 6. Repository

File `app/core/Repository.php` là lớp trung gian làm việc với database bằng PDO.

Repository có nhiệm vụ:

- Thực hiện SQL SELECT/INSERT/UPDATE/DELETE.
- Gom các nghiệp vụ quan trọng như đăng ký sự kiện, hủy đăng ký, xác nhận tham gia, QR check-in, cộng điểm, cấp chứng nhận.
- Dùng transaction cho các nghiệp vụ cần đảm bảo dữ liệu đồng bộ.

Repository đã được tách thêm các trait trong `app/core/repository`:

- `MemberRepositoryTrait`: tài khoản, đăng nhập, đổi mật khẩu.
- `ClubRepositoryTrait`: CLB, thành viên CLB, loại sự kiện.
- `EventRepositoryTrait`: sự kiện.
- `ContentRepositoryTrait`: nhóm học tập, điểm danh, bài đăng.
- `RegistrationCheckinPointRepositoryTrait`: đăng ký sự kiện, check-in, điểm rèn luyện, chứng nhận.
- `ReportRepositoryTrait`: báo cáo và thống kê.
- `ScopeRepositoryTrait`: kiểm tra phạm vi quản lý của trợ giảng/ban tổ chức.

Khi bảo vệ có thể nói: controller không viết SQL trực tiếp, mọi thao tác database đi qua Repository.

## 7. Validator

File `app/core/Validator.php` kiểm tra dữ liệu ở backend trước khi ghi database.

Các kiểm tra chính:

- Bắt buộc nhập.
- Email đúng định dạng.
- Độ dài tối đa.
- Số không âm.
- Học kỳ hợp lệ.
- Năm học đúng dạng `2024-2025`.
- Ngày kết thúc sau ngày bắt đầu.
- Thời gian đóng QR sau thời gian mở QR.
- File ảnh đúng định dạng JPG, PNG hoặc WEBP.

Điểm cần nhấn mạnh: hệ thống không chỉ dựa vào HTML/JavaScript, mà có validation phía PHP.

## 8. View

Các view nằm trong `app/views`.

Một số view dùng chung:

- `generic/list.php`: danh sách dữ liệu.
- `generic/form.php`: form thêm/sửa.
- `generic/details.php`: xem chi tiết.
- `generic/delete.php`: xác nhận xóa.
- `layouts/main.php`: layout chung.

View chỉ nên tập trung hiển thị HTML, không viết SQL.

## 9. API Và Fetch

File `app/controllers/Api_64131060Controller.php` trả dữ liệu JSON cho các thao tác dùng fetch/AJAX.

Một số API chính:

- Đăng ký sự kiện.
- Hủy đăng ký sự kiện.
- Xác nhận tham gia.
- Check-in sự kiện.
- Lấy điểm rèn luyện.
- Lấy chứng nhận.
- Lấy thống kê.

Frontend gọi các API này trong `public/Scripts/app-api.js`.

## 10. Cách Trình Bày Khi Bảo Vệ

Có thể trình bày theo thứ tự:

1. `index.php` là cửa vào.
2. `Router` phân tích URL.
3. `Controller` kiểm tra quyền và điều phối.
4. `Repository` xử lý SQL và nghiệp vụ.
5. `Validator` kiểm tra dữ liệu.
6. `View` hiển thị giao diện.
7. `API` hỗ trợ AJAX/fetch.

Câu tóm tắt:

```text
Dự án dùng MVC thủ công: request đi vào index.php, Router gọi Controller, Controller dùng Repository để xử lý dữ liệu, sau đó render View. Các thao tác quan trọng được validate ở backend và phân quyền bằng session role.
```
