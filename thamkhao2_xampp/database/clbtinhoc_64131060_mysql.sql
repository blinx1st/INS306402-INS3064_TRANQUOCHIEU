CREATE DATABASE IF NOT EXISTS clbtinhoc_64131060 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clbtinhoc_64131060;
SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS BaoCao;
DROP TABLE IF EXISTS BaiDang;
DROP TABLE IF EXISTS DiemDanh;
DROP TABLE IF EXISTS ThanhVienNhom;
DROP TABLE IF EXISTS NhomHocTap;
DROP TABLE IF EXISTS ThanhVienSuKien;
DROP TABLE IF EXISTS SuKien;
DROP TABLE IF EXISTS ThanhVien;
DROP TABLE IF EXISTS VaiTro;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE VaiTro (
    MaVaiTro VARCHAR(50) PRIMARY KEY,
    TenVaiTro VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ThanhVien (
    MaThanhVien VARCHAR(50) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    MatKhau VARCHAR(255) NOT NULL,
    MaVaiTro VARCHAR(50) NOT NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_thanhvien_vaitro FOREIGN KEY (MaVaiTro) REFERENCES VaiTro(MaVaiTro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE SuKien (
    MaSuKien VARCHAR(50) PRIMARY KEY,
    TenSuKien VARCHAR(100) NOT NULL,
    MoTa TEXT,
    NgayBatDau DATETIME NOT NULL,
    NgayKetThuc DATETIME NOT NULL,
    NguoiToChuc VARCHAR(50) NOT NULL,
    CONSTRAINT fk_sukien_nguoitochuc FOREIGN KEY (NguoiToChuc) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ThanhVienSuKien (
    MaSuKien VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    NgayDangKy DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (MaSuKien, MaThanhVien),
    CONSTRAINT fk_tvsk_sukien FOREIGN KEY (MaSuKien) REFERENCES SuKien(MaSuKien),
    CONSTRAINT fk_tvsk_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE NhomHocTap (
    MaNhom VARCHAR(50) PRIMARY KEY,
    TenNhom VARCHAR(100) NOT NULL,
    TroGiang VARCHAR(50) NOT NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    MoTa TEXT,
    CONSTRAINT fk_nhom_trogiang FOREIGN KEY (TroGiang) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ThanhVienNhom (
    MaNhom VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    NgayThamGia DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (MaNhom, MaThanhVien),
    CONSTRAINT fk_tvnhom_nhom FOREIGN KEY (MaNhom) REFERENCES NhomHocTap(MaNhom),
    CONSTRAINT fk_tvnhom_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE DiemDanh (
    MaDiemDanh INT PRIMARY KEY AUTO_INCREMENT,
    MaNhom VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    NgayDiemDanh DATE NOT NULL,
    TrangThai VARCHAR(50) NOT NULL,
    GhiChu TEXT,
    CONSTRAINT fk_diemdanh_nhom FOREIGN KEY (MaNhom) REFERENCES NhomHocTap(MaNhom),
    CONSTRAINT fk_diemdanh_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE BaiDang (
    MaBaiDang VARCHAR(50) PRIMARY KEY NOT NULL,
    TieuDe TEXT NOT NULL,
    Anh VARCHAR(100) NOT NULL,
    NoiDung TEXT NOT NULL,
    TacGia VARCHAR(50) NOT NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_baidang_tacgia FOREIGN KEY (TacGia) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE BaoCao (
    MaBaoCao INT PRIMARY KEY AUTO_INCREMENT,
    TieuDe VARCHAR(100) NOT NULL,
    NoiDung TEXT NOT NULL,
    NopBoi VARCHAR(50) NOT NULL,
    NgayNop DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_baocao_nopboi FOREIGN KEY (NopBoi) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO VaiTro (MaVaiTro,TenVaiTro) VALUES 
('TVCN','Chủ nhiệm'), 
('TVTG' ,'Thành viên trợ giảng'), 
('TV' ,'Thành viên thường');

-- Thêm dữ liệu mẫu vào bảng ThanhVien
INSERT INTO ThanhVien (MaThanhVien , HoTen, Email, MatKhau, MaVaiTro) VALUES
('64132127', 'Trần Thanh Thái', 'thai.tt.64cntt@ntu.edu.vn', '123', 'TVCN'),
('64131060', 'Phạm Tuấn Kiệt', 'kiet.pt.64cntt@ntu.edu.vn', '123', 'TVTG'),
('64132677', 'Vương Minh Trí', 'tri.vm.64cntt@ntu.edu.vn', '123', 'TVTG'),
('64130378', 'Trần Diệp Hồng Dung', 'dung.tdh.64cntt@ntu.edu.vn', '123', 'TV'),
('64132848', 'Trịnh Ngọc Tuấn', 'tuan.tn.64cntt@ntu.edu.vn', '123', 'TV'),
('64130493', 'Cao Linh Hà', 'ha.cl.64cntt@ntu.edu.vn', '123', 'TV'),
('64130152', 'Nguyễn Hồ Thanh Bình', 'binh.nht.64cntt@ntu.edu.vn', '123', 'TV'),
('64131973', 'Nguyễn Hiểu Quyên', 'quyen.nh.64cntt@ntu.edu.vn', '123', 'TV'),
('64132409', 'Vĩnh Thuận', 'thuan.v.64cntt@ntu.edu.vn', '123', 'TV'),
('64132902', 'Võ Văn Uy', 'uy.vv.64cntt@ntu.edu.vn', '123', 'TV'),
('64132079', 'Nguyễn Quốc Kỳ Tài', 'tai.nqk.64cntt@ntu.edu.vn', '123', 'TV'),
('64131375', 'Huỳnh Xuân Nam', 'nam.hx.64cntt@ntu.edu.vn', '123', 'TV'),
('64131236', 'Lê Văn Lương', 'luong.lv.64cntt@ntu.edu.vn', '123', 'TV'),
('64131228', 'Nguyễn Đỗ Thiên Luân', 'luan.ndt.64cntt@ntu.edu.vn', '123', 'TV'),
('64131209', 'Huỳnh Ngọc Long', 'long.hn.64cntt@ntu.edu.vn', '123', 'TV'),
('64131003', 'Đặng Nguyến Đăng Khoa', 'khoa.dnd.64cntt@ntu.edu.vn', '123', 'TV'),
('64130939', 'Lê Ngọc Khải', 'khai.ln.64cntt@ntu.edu.vn', '123', 'TV'),
('64130227', 'Phạm Mạnh Cường', 'cuong.pm.64cntt@ntu.edu.vn', '123', 'TV'),
('64130729', 'Lê Việt Hoàng', 'hoang.lv.64cntt@ntu.edu.vn', '123', 'TV'),
('64130262', 'Trần Hoàng Đạo', 'dao.th.64cntt@ntu.edu.vn', '123', 'TV'),
('64131858', 'Nguyễn Thành Phước', 'phuoc.nt.64cntt@ntu.edu.vn', '123', 'TV'),
('64132058', 'Nguyễn Hải Sơn', 'son.nh.64cntt@ntu.edu.vn', '123', 'TV'),
('64132800', 'Nguyễn Hoàng Minh Tú', 'tu.nhm.64cntt@ntu.edu.vn', '123', 'TV'),
('64130134', 'Trần Lương Gia Bảo', 'bao.tlg.64cntt@ntu.edu.vn', '123', 'TV'),
('64130848', 'Lê Quang Huy', 'huy.lq.64cntt@ntu.edu.vn', '123', 'TV'),
('64131537', 'Nguyễn Đình Nguyên', 'nguyen.nd.64cntt@ntu.edu.vn', '123', 'TV'),
('64132295', 'Nguyễn Diệp Trường Thịnh', 'thinh.ndt.64cntt@ntu.edu.vn', '123', 'TV'),
('64132319', 'Nguyễn Hữu Thọ', 'tho.nh.64cntt@ntu.edu.vn', '123', 'TV'),
('64132534', 'Huỳnh Nguyễn Thương Tín', 'tin.hnt.64cntt@ntu.edu.vn', '123', 'TV'),
('64132201', 'Võ Văn Thành', 'thanh.vv.64cntt@ntu.edu.vn', '123', 'TV');

-- Thêm dữ liệu mẫu vào bảng SuKien
INSERT INTO SuKien (MaSuKien, TenSuKien, MoTa, NgayBatDau, NgayKetThuc, NguoiToChuc) VALUES
('SK001', 'Workshop Kỹ năng', 'Buổi workshop về kỹ năng làm việc nhóm.', '2024-12-01', '2024-12-01', '64132127'),
('SK002', 'Hackathon', 'Cuộc thi lập trình kéo dài 48 giờ.', '2024-12-15', '2024-12-17', '64132127'),
('SK003', 'Chào đón Tân Sinh Viên', 'Hoạt động chào đón và định hướng.', '2024-11-30', '2024-11-30', '64132127'),
('SK004', 'Buổi học Python cơ bản', 'Dành cho người mới bắt đầu học lập trình Python.', '2024-12-05', '2024-12-05', '64131060'),
('SK005', 'Seminar Công nghệ', 'Thảo luận về các xu hướng công nghệ mới.', '2024-12-10', '2024-12-10', '64132677'),
('SK006', 'Ngày Nhà Giáo Việt Nam 20 - 11', 'Chào mừng ngày nhà giáo Việt Nam', '2024-11-20', '2024-11-20', '64132127');

-- Thêm dữ liệu mẫu vào bảng ThanhVienSuKien
INSERT INTO ThanhVienSuKien (MaSuKien, MaThanhVien) VALUES
('SK001', '64132409'),
('SK001', '64132534'),
('SK001', '64131209'),
('SK001', '64132201'),
('SK001', '64130134'),
('SK002', '64130493'),
('SK002', '64131973'),
('SK002', '64132079'),
('SK002', '64130152'),
('SK002', '64130939'),
('SK003', '64130227'),
('SK003', '64131003'),
('SK003', '64131375'),
('SK003', '64132848'),
('SK003', '64130378'),
('SK004', '64132409'),
('SK004', '64132534'),
('SK004', '64132319'),
('SK004', '64132201'),
('SK004', '64130134'),
('SK005', '64130493'),
('SK005', '64131973'),
('SK005', '64132079'),
('SK005', '64130152'),
('SK005', '64130939'),
('SK006', '64130227'),
('SK006', '64131003'),
('SK006', '64131375'),
('SK006', '64132848'),
('SK006', '64130378');

-- Thêm dữ liệu mẫu vào bảng NhomHocTap
INSERT INTO NhomHocTap (MaNhom, TenNhom, TroGiang, MoTa) VALUES
('MNLT', 'Nhóm Nhập môn lập trình', '64132127','Nhóm học tập về lập trình căn bản.'),
('KTLT', 'Nhóm Kỹ thuật lập trình', '64132677','Nhóm học tập về lập trình nâng cao.'),
('PTUDW', 'Nhóm Web', '64131060' , 'Nhóm phát triển ứng dụng Web.'),
('MMT', 'Nhóm Mạng máy tính', '64132127', 'Nghiên cứu về mạng máy tính.');

-- Thêm dữ liệu mẫu vào bảng ThanhVienNhom
INSERT INTO ThanhVienNhom (MaNhom, MaThanhVien) VALUES
('MNLT', '64130378'),
('MNLT', '64132848'),
('MNLT', '64130493'),
('MNLT', '64130152'),
('MNLT', '64131973'),
('MNLT', '64132409'),
('KTLT', '64130378'),
('KTLT', '64131375'),
('KTLT', '64130493'),
('KTLT', '64132201'),
('KTLT', '64131209'),
('KTLT', '64132800'),
('PTUDW', '64130134'),
('PTUDW', '64131236'),
('PTUDW', '64130493'),
('PTUDW', '64131228'),
('PTUDW', '64131209'),
('PTUDW', '64132534'),
('MMT', '64131003'),
('MMT', '64130848'),
('MMT', '64130939'),
('MMT', '64131973'),
('MMT', '64132058'),
('MMT', '64131858');

-- Thêm dữ liệu mẫu vào bảng DiemDanh
INSERT INTO DiemDanh (MaNhom, MaThanhVien, NgayDiemDanh, TrangThai, GhiChu) VALUES
('MNLT', '64130378', '2024-10-6', 'Có mặt',''),
('MNLT', '64132848', '2024-10-6', 'Vắng mặt','Lý do vắng: Có việc đột xuất'),
('MNLT', '64130493', '2024-10-6', 'Có mặt',''),
('MNLT', '64130152', '2024-10-6', 'Có mặt',''),
('MNLT', '64131973', '2024-10-6', 'Có mặt',''),
('MNLT', '64132409', '2024-10-6', 'Có mặt',''),
('KTLT', '64130378', '2024-10-10', 'Có mặt',''),
('KTLT', '64131375', '2024-10-10', 'Vắng mặt','Lý do vắng: Bệnh sốt'),
('KTLT', '64130493', '2024-10-10', 'Có mặt',''),
('KTLT', '64132201', '2024-10-10', 'Có mặt',''),
('KTLT', '64131209', '2024-10-10', 'Có mặt',''),
('KTLT', '64132800', '2024-10-10', 'Có mặt',''),
('PTUDW', '64130134', '2024-10-5', 'Có mặt',''),
('PTUDW', '64131236', '2024-10-5', 'Vắng mặt','Lý do vắng: trùng lịch học'),
('PTUDW', '64130493', '2024-10-5', 'Có mặt',''),
('PTUDW', '64131228', '2024-10-5', 'Có mặt',''),
('PTUDW', '64131209', '2024-10-5', 'Có mặt',''),
('PTUDW', '64132534', '2024-10-5', 'Có mặt',''),
('MMT', '64131003', '2024-10-7', 'Có mặt',''),
('MMT', '64130848', '2024-10-7', 'Vắng mặt','Lý do vắng: trùng lịch thi'),
('MMT', '64130939', '2024-10-7', 'Có mặt',''),
('MMT', '64131973', '2024-10-7', 'Có mặt',''),
('MMT', '64132058', '2024-10-7', 'Có mặt',''),
('MMT', '64131858', '2024-10-7', 'Có mặt','');

-- Thêm dữ liệu mẫu vào bảng BaiDang
INSERT INTO BaiDang (MaBaiDang ,TieuDe ,Anh , NoiDung, TacGia) VALUES
('BD001', 'Thành viên mới và 10 vạn câu hỏi cần giải đáp', 'BD001.jpg', 'Thành viên mới và 10 vạn câu hỏi cần giải đáp', '64132127'),
('BD002', 'THÔNG BÁO TỪ CLB TIN HỌC', 'B002.jpg', 'Thông báo lịch học', '64132127'),
('BD003', 'CHÚC MỪNG NGÀY PHỤ NỮ VIỆT NAM 20/10', 'B003.jpg', 'Nhân dịp ngày 20/10 – Ngày Phụ nữ Việt Nam, Câu lạc bộ Tin học xin gửi lời chúc tốt đẹp nhất đến những nụ hồng IT đáng iu. Chúc các bạn luôn xinh đẹp, hạnh phúc, tự tin và thành công trong học tập, công việc cũng như trong cuộc sống.', '64132127'),
('BD004', 'CUỘC THI CHÀO MỪNG NGÀY NHÀ GIÁO VIỆT NAM 10/11', 'BD0004.jpg','Cuộc thi chào mừng ngày nhà giáo Việt Nam 20/11', '64131060');

-- Thêm dữ liệu mẫu vào bảng BaoCao
INSERT INTO BaoCao (TieuDe, NoiDung, NopBoi) VALUES
('Báo cáo Workshop Kỹ năng', 'Tổng kết buổi workshop ngày 01/12.', '64132127'),
('Báo cáo cuộc thi Hackathon', 'Báo cáo kết quả cuộc thi Hackathon.', '64132127'),
('Báo cáo chào đón Tân Sinh Viên', 'Báo cáo Hoạt động chào đón và định hướng.', '64132127'),
('Báo cáo buổi học Python cơ bản', 'Báo cáo kết quả buổi học Python cơ bản.', '64131060'),
('Báo cáo Seminar Công nghệ', 'Báo cáo thảo luận về các xu hướng công nghệ mới.', '64132677'),
('Báo cáo cuộc thi chào mừng ngày giáo Việt Nam', 'Báo cáo kết quả cuộc thi chào mừng ngày nhà giáo Việt Nam.', '64132127');

