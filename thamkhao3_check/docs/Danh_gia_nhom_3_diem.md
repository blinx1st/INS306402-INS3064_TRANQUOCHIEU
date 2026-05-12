# Danh Gia Nhom - 3 Diem

Tai lieu nay duoc viet dua tren du an Web Quan ly CLB Tin Hoc va Su kien sinh vien. Co the dung de dua vao bao cao, slide thuyet trinh hoac lam kich ban bao ve.

## 1. Thuyet Trinh Du An - 1.0 Diem

### Muc tieu phan nay

- Gioi thieu ro van de, boi canh nghiep vu va pham vi du an.
- Trinh bay tong quan he thong, cac module chinh va phan cong nhiem vu.
- Noi ngan gon, logic, bang tieng Anh.

### English Presentation Script

Good morning teacher and everyone.

Our project is a Student Club and Event Management Web Application. The main problem we want to solve is that student clubs usually manage events, member registration, attendance, activity points and certificates manually. This can lead to duplicated registrations, missing attendance records, incorrect activity points and difficulties when exporting data at the end of the semester.

The scope of our project focuses on the Informatics Club. The system supports three main user roles: Admin or Student Affairs Office, Club Organizer or Assistant, and Student. Each role has different permissions and different screens after login.

For students, the system allows them to register an account, log in, view club events, register for events, cancel registration before check-in, scan a QR code to check in, view their activity points and view or print their certificates.

For club organizers, the system supports event management, registration list management, QR check-in management and attendance confirmation. Organizers can monitor how many students registered and how many students actually checked in.

For administrators, the system supports account management, club management, event management, activity point rule configuration, certificate management and statistical reports. The admin can also export activity point data for semester evaluation.

Technically, the project is built with pure PHP using a simple MVC structure. We use PDO to connect to MySQL or MariaDB. The database is normalized and includes related tables such as users, roles, clubs, club members, events, event registrations, check-in logs, activity point rules, total points and certificates.

The system also includes backend authorization guards, server-side validation, client-side validation and AJAX or fetch API for important actions such as confirming attendance and checking event data without reloading the page.

Task assignment in our team:

- Member 1 is responsible for database design, account management, role authorization and club management.
- Member 2 is responsible for event management, event registration, QR check-in and check-in logs.
- Member 3 is responsible for activity points, certificates, reports, user interface improvement and documentation.

In summary, our system provides a complete workflow from creating events, registering students, checking attendance, calculating activity points, generating certificates and exporting final data. This helps the club and the Student Affairs Office manage event activities more accurately and efficiently.

Thank you for listening. Now we will start the system demo.

### Slide Outline Goi Y

1. Project title: Student Club and Event Management System.
2. Problem and business context.
3. Project scope and target users.
4. System roles: Admin, Club Organizer, Student.
5. Main modules: account, club, event, registration, QR check-in, points, certificates, reports.
6. Database overview and important relationships.
7. Team task assignment.
8. Demo flow.
9. Conclusion.

## 2. Demo He Thong - 1.0 Diem

### Muc tieu phan nay

- Chung minh website chay duoc tren XAMPP.
- Demo cac module co lien ket voi nhau, khong chi demo tung trang rieng le.
- Tap trung vao quy trinh nghiep vu tieu bieu: tao su kien -> dang ky -> check-in -> cong diem -> cap chung nhan -> bao cao.

### Chuan bi truoc khi demo

- Bat Apache va MySQL trong XAMPP.
- Import database: `database/clbtinhoc_64131060_mysql.sql`.
- Mo website:

```text
http://localhost/ins3064/INS306402-INS3064_TRANQUOCHIEU/thamkhao3_check/public/
```

- Tai khoan mau:

```text
Admin/Chu nhiem: quochieuu@vnuis.edu.vn / 123
Tro giang/Ban to chuc: trang@vnuis.edu.vn / 123
Sinh vien/Thanh vien: hongphuong@vnuis.edu.vn / 123
```

### Demo Flow De Xuat

#### Buoc 1: Dang nhap va phan quyen

1. Mo trang dang nhap.
2. Dang nhap bang tai khoan Admin.
3. Gioi thieu menu Admin: quan ly thanh vien, CLB, su kien, diem ren luyen, chung nhan, bao cao.
4. Dang xuat, dang nhap bang tai khoan Sinh vien.
5. Chi ra menu Sinh vien it hon, khong co chuc nang quan tri.
6. Giai thich he thong co backend guard, nen khong chi an menu ma con chan truy cap URL sai quyen.

#### Buoc 2: Quan ly CLB

1. Dang nhap Admin.
2. Mo module CLB.
3. Demo them/sua/xem danh sach CLB.
4. Mo module ThanhVienCLB de gan thanh vien vao CLB.
5. Giai thich su kien se duoc gan voi CLB to chuc.

#### Buoc 3: Quan ly su kien va suc chua

1. Mo module SuKien.
2. Them hoac sua mot su kien.
3. Chi ra cac truong quan trong:
   - CLB to chuc.
   - Loai su kien.
   - Hoc ky, nam hoc.
   - Ngay bat dau, ngay ket thuc.
   - Suc chua.
   - Thoi gian mo/dong check-in.
4. Giai thich validation: bat buoc nhap, ngay ket thuc phai sau ngay bat dau, suc chua hop le.

#### Buoc 4: Sinh vien dang ky su kien

1. Dang nhap tai khoan Sinh vien.
2. Mo danh sach su kien.
3. Chon mot su kien va dang ky tham gia.
4. Giai thich he thong kiem tra:
   - Sinh vien da dang ky chua.
   - Su kien con cho khong.
   - Neu da huy thi co the dang ky lai.
5. Neu can, demo chuc nang huy dang ky khi chua check-in.

#### Buoc 5: QR check-in su kien

1. Dang nhap Admin hoac Tro giang.
2. Mo trang QR cua su kien.
3. QR code chua link noi bo dang:

```text
/CheckInSuKien_64131060/Scan?MaSuKien=...&Token=...
```

4. Dang nhap Sinh vien va mo link QR.
5. He thong kiem tra:
   - Sinh vien da dang nhap va co role TV.
   - Token QR dung voi su kien.
   - QR dang nam trong khoang CheckinMoLuc - CheckinDongLuc.
   - Sinh vien da dang ky su kien.
   - Sinh vien chua check-in truoc do.
6. Neu hop le, he thong ghi log vao bang `CheckinSuKien`.
7. Thu quet lai lan 2 de demo he thong chan check-in trung.

#### Buoc 6: Cong diem va cap chung nhan

1. Sau khi check-in thanh cong, he thong cap nhat trang thai tham gia la `Da tham gia`.
2. He thong tim quy tac diem theo loai su kien, hoc ky va nam hoc.
3. He thong tao ban ghi diem trong `DiemRenLuyen`.
4. He thong cap nhat tong diem trong `TongDiemRenLuyen`.
5. He thong tao chung nhan trong `ChungNhan`.
6. Mo tai khoan Sinh vien de xem diem va chung nhan cua chinh minh.
7. Mo trang in chung nhan va dung nut in cua trinh duyet neu can.

#### Buoc 7: Bao cao va export

1. Dang nhap Admin.
2. Mo dashboard bao cao thong ke.
3. Trinh bay cac so lieu:
   - Tong so su kien.
   - Tong so sinh vien dang ky.
   - Tong so luot check-in.
   - Tong diem hoat dong da cong.
   - Thong ke theo CLB.
   - Thong ke theo hoc ky/nam hoc.
4. Mo module diem ren luyen va export CSV cuoi ky.
5. Giai thich file CSV dung UTF-8 BOM de mo bang Excel.

### Cau Noi Khi Demo

Du an khong chi co cac trang CRUD rieng le. Cac module duoc ket noi thanh mot quy trinh nghiep vu hoan chinh: Admin tao CLB va su kien, sinh vien dang ky, he thong kiem tra suc chua, sinh vien check-in bang QR, he thong ghi log, chan check-in trung, tu dong cong diem, tao chung nhan va tong hop bao cao cuoi ky.

## 3. Chat Luong Bao Cao Va Bai Nop - 1.0 Diem

### Muc tieu phan nay

- Bai nop phai du source code, database, bao cao va slide.
- Tai lieu phai sap xep ro rang de giang vien co the cai dat, chay va danh gia nhanh.
- Noi dung bao cao can phan anh dung he thong da lam.

### Cau Truc Bai Nop De Xuat

```text
thamkhao3_check/
|-- app/
|   |-- config/
|   |-- controllers/
|   |-- core/
|   |-- views/
|-- public/
|   |-- Content/
|   |-- Image/
|   |-- Scripts/
|   |-- index.php
|-- database/
|   |-- clbtinhoc_64131060_mysql.sql
|-- docs/
|   |-- Giai_thich_bo_sung_chuc_nang.docx
|   |-- Danh_gia_nhom_3_diem.md
|-- README.md
```

### Noi Dung Bao Cao Can Co

1. Ten de tai: Web quan ly su kien va cau lac bo sinh vien.
2. Ly do chon de tai va boi canh nghiep vu.
3. Muc tieu cua he thong.
4. Pham vi chuc nang.
5. Cong nghe su dung:
   - PHP thuan.
   - HTML, CSS, JavaScript.
   - MySQL/MariaDB.
   - PDO.
   - MVC thu cong.
6. Mo ta phan quyen:
   - TVCN: Admin/Phong CTSV.
   - TVTG: Ban to chuc/Tro giang.
   - TV: Sinh vien/Thanh vien.
7. Mo ta co so du lieu:
   - VaiTro.
   - ThanhVien.
   - CLB.
   - ThanhVienCLB.
   - SuKien.
   - ThanhVienSuKien.
   - CheckinSuKien.
   - LoaiSuKien.
   - QuyTacDiemRenLuyen.
   - DiemRenLuyen.
   - TongDiemRenLuyen.
   - ChungNhan.
   - Cac bang phu nhu BaiDang, NhomHocTap, DiemDanh, BaoCao.
8. Mo ta cac quy trinh chinh:
   - Dang nhap va phan quyen.
   - Tao su kien.
   - Dang ky su kien.
   - Huy dang ky.
   - QR check-in.
   - Cong diem tu dong.
   - Cap chung nhan.
   - Bao cao thong ke va export.
9. Mo ta validation:
   - Bat buoc nhap.
   - Email hop le.
   - Do dai du lieu.
   - Ngay ket thuc sau ngay bat dau.
   - Diem khong am.
   - File anh dung dinh dang.
10. Mo ta API/AJAX:

- Dang ky su kien.
- Huy dang ky.
- Xac nhan tham gia.
- Check-in su kien.
- Lay diem ren luyen.
- Lay chung nhan.
- Lay thong ke.

11. Huong dan cai dat va chay chuong trinh.
12. Tai khoan mau de test.
13. Ket qua dat duoc.
14. Han che va huong phat trien.

### Noi Dung Slide Can Co

- Slide 1: Ten de tai va thong tin nhom.
- Slide 2: Van de thuc te.
- Slide 3: Muc tieu va pham vi.
- Slide 4: Vai tro nguoi dung.
- Slide 5: Chuc nang chinh.
- Slide 6: So do CSDL/ERD.
- Slide 7: Kien truc MVC va cong nghe.
- Slide 8: Quy trinh QR check-in va cong diem.
- Slide 9: Demo flow.
- Slide 10: Ket qua, ket luan va huong phat trien.

### Checklist Truoc Khi Nop

- Source code day du va khong thieu folder `app`, `public`, `database`.
- File database `.sql` import duoc trong phpMyAdmin.
- `README.md` co huong dan cai dat, chay web va tai khoan mau.
- Bao cao co mo ta database, chuc nang, phan quyen va quy trinh nghiep vu.
- Slide ngan gon, dung luong vua phai, co hinh minh hoa giao dien.
- File anh, CSS, JavaScript va upload mau neu co duoc dat dung thu muc.
- Cac route chinh chay duoc khi demo.
- Tai khoan Admin, Tro giang va Sinh vien dang nhap duoc.
- Khong de mat khau database that neu nop public.
- Neu co video demo, dat trong folder rieng hoac nop kem link.

### Phan Danh Gia Tuong Ung 1.0 Diem

Bai nop dat yeu cau khi nguoi cham co the mo README, import database, chay website, dang nhap tai khoan mau va kiem tra duoc cac module chinh ma khong can hoi lai nhom. Source code, database, bao cao va slide phai thong nhat voi nhau.
