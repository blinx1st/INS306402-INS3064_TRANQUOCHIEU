# Luồng Nghiệp Vụ Chính Của Website

Tài liệu này giúp giải thích các chức năng quan trọng khi demo và bảo vệ cuối kỳ.

## 1. Đăng Nhập Và Phân Quyền

Người dùng đăng nhập bằng email và mật khẩu.

Sau khi đăng nhập thành công, hệ thống lưu thông tin vào `$_SESSION`:

- Email.
- Mã thành viên.
- Họ tên.
- Mã vai trò.

Ba vai trò chính:

- `TVCN`: Admin/Phòng CTSV.
- `TVTG`: Ban tổ chức/Trợ giảng.
- `TV`: Sinh viên/Thành viên.

Controller dùng `requireLogin()` và `requireRoles([...])` để chặn truy cập sai quyền. Vì vậy hệ thống không chỉ ẩn menu, mà còn chặn trực tiếp URL nếu người dùng không đủ quyền.

## 2. Quản Lý CLB

Admin có thể quản lý danh sách câu lạc bộ.

Các bảng liên quan:

- `CLB`: thông tin câu lạc bộ.
- `ThanhVienCLB`: thành viên thuộc từng CLB và vai trò trong CLB.

Ý nghĩa nghiệp vụ:

- Một CLB có nhiều thành viên.
- Một sự kiện được gắn với CLB tổ chức.
- Trợ giảng/ban tổ chức chỉ quản lý dữ liệu thuộc CLB mình phụ trách.

## 3. Quản Lý Sự Kiện

Admin hoặc Ban tổ chức tạo sự kiện.

Thông tin quan trọng của sự kiện:

- CLB tổ chức.
- Loại sự kiện.
- Học kỳ và năm học.
- Ngày bắt đầu, ngày kết thúc.
- Sức chứa.
- Thời gian mở và đóng QR check-in.
- Token check-in.

Validation quan trọng:

- Tên sự kiện không được trống.
- Sức chứa phải lớn hơn 0.
- Ngày kết thúc phải sau ngày bắt đầu.
- Thời gian đóng QR phải sau thời gian mở QR.

## 4. Sinh Viên Đăng Ký Sự Kiện

Sinh viên đăng nhập bằng role `TV`, mở danh sách sự kiện và bấm đăng ký.

Hệ thống kiểm tra:

- Sự kiện có tồn tại không.
- Sinh viên đã đăng ký sự kiện này chưa.
- Nếu đăng ký đã hủy thì cho đăng ký lại.
- Sự kiện còn chỗ hay đã đủ sức chứa.

Nếu hợp lệ, hệ thống ghi vào bảng `ThanhVienSuKien` với trạng thái `Đã đăng ký`.

## 5. Hủy Đăng Ký

Sinh viên có thể hủy đăng ký nếu chưa check-in.

Hệ thống kiểm tra:

- Có bản ghi đăng ký không.
- Trạng thái chưa phải `Đã tham gia`.
- Chưa có log trong `CheckinSuKien`.

Nếu hợp lệ, trạng thái đổi thành `Đã hủy`. Hệ thống giữ lịch sử thay vì xóa cứng bản ghi đăng ký.

## 6. QR Check-in

Admin hoặc Ban tổ chức mở QR của sự kiện.

QR chứa link nội bộ dạng:

```text
/CheckInSuKien_64131060/Scan?MaSuKien=...&Token=...
```

Khi sinh viên quét QR, hệ thống kiểm tra:

- Sinh viên đã đăng nhập và có role `TV`.
- Mã sự kiện tồn tại.
- Token trong QR đúng với token của sự kiện.
- QR đang trong thời gian hiệu lực `CheckinMoLuc` đến `CheckinDongLuc`.
- Sinh viên đã đăng ký sự kiện.
- Sinh viên chưa check-in trước đó.

Nếu hợp lệ, hệ thống ghi log vào `CheckinSuKien`.

## 7. Chống Check-in Trùng

Bảng `CheckinSuKien` có unique key theo cặp:

```text
MaSuKien + MaThanhVien
```

Ý nghĩa:

- Một sinh viên chỉ được check-in một lần cho một sự kiện.
- Sinh viên vẫn có thể check-in nhiều sự kiện khác nhau.
- Một sự kiện vẫn có nhiều sinh viên check-in.

Ngoài database constraint, code cũng kiểm tra trước để báo lỗi rõ ràng.

## 8. Cộng Điểm Rèn Luyện

Điểm không nhập thủ công trực tiếp ở bảng `DiemRenLuyen`.

Admin cấu hình điểm trong bảng `QuyTacDiemRenLuyen` theo:

- Loại sự kiện.
- Học kỳ.
- Năm học.

Khi sinh viên được xác nhận tham gia hoặc check-in thành công:

1. Hệ thống tìm quy tắc điểm phù hợp.
2. Tạo hoặc cập nhật dòng điểm trong `DiemRenLuyen`.
3. Cập nhật tổng điểm trong `TongDiemRenLuyen`.

Nếu chưa có quy tắc điểm, hệ thống báo lỗi để Admin cấu hình trước.

## 9. Cấp Chứng Nhận

Sau khi sinh viên tham gia hợp lệ, hệ thống tự tạo chứng nhận trong bảng `ChungNhan`.

Mã chứng nhận có dạng:

```text
CN-{MaSuKien}-{MaThanhVien}
```

Sinh viên có thể xem và in chứng nhận của chính mình. Admin và Ban tổ chức có thể quản lý chứng nhận theo quyền.

## 10. Báo Cáo Và Export

Admin có thể xem dashboard thống kê:

- Tổng số sự kiện.
- Tổng số đăng ký.
- Tổng số lượt check-in.
- Tổng điểm đã cộng.
- Thống kê theo CLB.
- Top sinh viên tích cực.

Admin cũng có thể export CSV điểm rèn luyện cuối kỳ. File CSV dùng UTF-8 BOM để mở bằng Excel không bị lỗi tiếng Việt.

## 11. Demo Flow Khuyến Nghị

Khi demo nên đi theo một luồng hoàn chỉnh:

1. Admin đăng nhập.
2. Admin tạo CLB hoặc kiểm tra CLB có sẵn.
3. Admin tạo sự kiện có sức chứa và thời gian QR.
4. Admin cấu hình quy tắc điểm.
5. Sinh viên đăng nhập và đăng ký sự kiện.
6. Sinh viên quét QR check-in.
7. Hệ thống chặn check-in trùng nếu quét lại.
8. Sinh viên xem điểm và chứng nhận.
9. Admin xem báo cáo và export CSV.

Câu tóm tắt:

```text
Luồng chính của hệ thống là: tạo sự kiện, sinh viên đăng ký, check-in bằng QR, hệ thống ghi log, chống trùng, tự cộng điểm theo quy tắc, cấp chứng nhận và xuất báo cáo cuối kỳ.
```
