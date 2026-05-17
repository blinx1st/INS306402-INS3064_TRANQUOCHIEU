<?php
class Repository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function options(array $relation): array
    {
        $sql = sprintf('SELECT %s AS value, %s AS label FROM %s ORDER BY %s', $relation['value'], $relation['label'], $relation['table'], $relation['label']);
        return $this->db->query($sql)->fetchAll();
    }

    public function listMembers(?string $maThanhVien = null): array
    {
        $sql = 'SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro';
        $params = [];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $sql .= ' WHERE ThanhVien.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        $sql .= ' ORDER BY ThanhVien.MaThanhVien ASC';
        return $this->fetchAll($sql, $params);
    }

    public function searchMembers(string $maThanhVien, string $hoTen): array
    {
        return $this->fetchAll("SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro
            WHERE (:ma = '' OR ThanhVien.MaThanhVien LIKE :maLike)
                AND (:ten = '' OR ThanhVien.HoTen LIKE :tenLike)
            ORDER BY ThanhVien.MaThanhVien ASC", [
            'ma' => $maThanhVien,
            'maLike' => '%' . $maThanhVien . '%',
            'ten' => $hoTen,
            'tenLike' => '%' . $hoTen . '%',
        ]);
    }

    public function findMember(string $maThanhVien): ?array
    {
        return $this->fetchOne('SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro
            WHERE ThanhVien.MaThanhVien = :maThanhVien
            LIMIT 1', ['maThanhVien' => $maThanhVien]);
    }

    public function findMemberByEmail(?string $email): ?array
    {
        if (!$email) {
            return null;
        }
        return $this->fetchOne('SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro
            WHERE ThanhVien.Email = :email
            LIMIT 1', ['email' => $email]);
    }

    public function createMember(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ThanhVien (MaThanhVien, HoTen, Email, MatKhau, MaVaiTro, NgayTao) VALUES (:MaThanhVien, :HoTen, :Email, :MatKhau, :MaVaiTro, :NgayTao)');
        $stmt->execute([
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'HoTen' => $data['HoTen'] ?? '',
            'Email' => $data['Email'] ?? '',
            'MatKhau' => $data['MatKhau'] ?? '',
            'MaVaiTro' => $data['MaVaiTro'] ?? 'TV',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateMember(string $maThanhVien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVien SET HoTen = :HoTen, Email = :Email, MatKhau = :MatKhau, MaVaiTro = :MaVaiTro, NgayTao = :NgayTao WHERE MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'HoTen' => $data['HoTen'] ?? '',
            'Email' => $data['Email'] ?? '',
            'MatKhau' => $data['MatKhau'] ?? '',
            'MaVaiTro' => $data['MaVaiTro'] ?? 'TV',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function deleteMember(string $maThanhVien): void
    {
        $stmt = $this->db->prepare('DELETE FROM ThanhVien WHERE MaThanhVien = :MaThanhVien');
        $stmt->execute(['MaThanhVien' => $maThanhVien]);
    }

    public function login(string $email, string $password): ?array
    {
        return $this->fetchOne('SELECT * FROM ThanhVien WHERE Email = :email AND MatKhau = :password LIMIT 1', ['email' => $email, 'password' => $password]);
    }

    public function listEventTypes(): array
    {
        return $this->db->query('SELECT LoaiSuKien.* FROM LoaiSuKien ORDER BY LoaiSuKien.MaLoaiSuKien ASC')->fetchAll();
    }

    public function findEventType(string $maLoaiSuKien): ?array
    {
        return $this->fetchOne('SELECT LoaiSuKien.* FROM LoaiSuKien WHERE MaLoaiSuKien = :MaLoaiSuKien LIMIT 1', ['MaLoaiSuKien' => $maLoaiSuKien]);
    }

    public function createEventType(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO LoaiSuKien (MaLoaiSuKien, TenLoaiSuKien, MoTa) VALUES (:MaLoaiSuKien, :TenLoaiSuKien, :MoTa)');
        $stmt->execute([
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'TenLoaiSuKien' => $data['TenLoaiSuKien'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
        ]);
    }

    public function updateEventType(string $maLoaiSuKien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE LoaiSuKien SET TenLoaiSuKien = :TenLoaiSuKien, MoTa = :MoTa WHERE MaLoaiSuKien = :MaLoaiSuKien');
        $stmt->execute([
            'TenLoaiSuKien' => $data['TenLoaiSuKien'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'MaLoaiSuKien' => $maLoaiSuKien,
        ]);
    }

    public function deleteEventType(string $maLoaiSuKien): void
    {
        $stmt = $this->db->prepare('DELETE FROM LoaiSuKien WHERE MaLoaiSuKien = :MaLoaiSuKien');
        $stmt->execute(['MaLoaiSuKien' => $maLoaiSuKien]);
    }

    public function listClubs(?string $assistantId = null): array
    {
        $sql = $this->clubSelectSql();
        $params = [];
        if ($assistantId) {
            $sql .= ' WHERE ' . $this->assistantClubWhere();
            $params = ['assistantOwner' => $assistantId, 'assistantClubMember' => $assistantId];
        }
        $sql .= ' ORDER BY CLB.MaCLB ASC';
        return $this->fetchAll($sql, $params);
    }

    public function findClub(string $maCLB): ?array
    {
        return $this->fetchOne($this->clubSelectSql() . ' WHERE CLB.MaCLB = :MaCLB LIMIT 1', ['MaCLB' => $maCLB]);
    }

    public function createClub(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO CLB (MaCLB, TenCLB, MoTa, ChuNhiem, NgayThanhLap) VALUES (:MaCLB, :TenCLB, :MoTa, :ChuNhiem, :NgayThanhLap)');
        $stmt->execute([
            'MaCLB' => $data['MaCLB'] ?? '',
            'TenCLB' => $data['TenCLB'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'ChuNhiem' => $data['ChuNhiem'] ?? null,
            'NgayThanhLap' => $data['NgayThanhLap'] ?? null,
        ]);
    }

    public function updateClub(string $maCLB, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE CLB SET TenCLB = :TenCLB, MoTa = :MoTa, ChuNhiem = :ChuNhiem, NgayThanhLap = :NgayThanhLap WHERE MaCLB = :MaCLB');
        $stmt->execute([
            'TenCLB' => $data['TenCLB'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'ChuNhiem' => $data['ChuNhiem'] ?? null,
            'NgayThanhLap' => $data['NgayThanhLap'] ?? null,
            'MaCLB' => $maCLB,
        ]);
    }

    public function deleteClub(string $maCLB): void
    {
        $stmt = $this->db->prepare('DELETE FROM CLB WHERE MaCLB = :MaCLB');
        $stmt->execute(['MaCLB' => $maCLB]);
    }

    public function listClubMembers(?string $assistantId = null): array
    {
        $sql = $this->clubMemberSelectSql();
        $params = [];
        if ($assistantId) {
            $sql .= ' WHERE ThanhVienCLB.MaCLB IN (' . $this->assistantClubSubquery() . ')';
            $params['assistantClubMember'] = $assistantId;
        }
        $sql .= ' ORDER BY CLB.TenCLB ASC, ThanhVien.HoTen ASC';
        return $this->fetchAll($sql, $params);
    }

    public function findClubMember(string $maCLB, string $maThanhVien): ?array
    {
        return $this->fetchOne($this->clubMemberSelectSql() . ' WHERE ThanhVienCLB.MaCLB = :MaCLB AND ThanhVienCLB.MaThanhVien = :MaThanhVien LIMIT 1', [
            'MaCLB' => $maCLB,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function createClubMember(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ThanhVienCLB (MaCLB, MaThanhVien, VaiTroCLB, NgayThamGia) VALUES (:MaCLB, :MaThanhVien, :VaiTroCLB, :NgayThamGia)');
        $stmt->execute([
            'MaCLB' => $data['MaCLB'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'VaiTroCLB' => $data['VaiTroCLB'] ?? '',
            'NgayThamGia' => $data['NgayThamGia'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateClubMember(string $maCLB, string $maThanhVien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVienCLB SET VaiTroCLB = :VaiTroCLB, NgayThamGia = :NgayThamGia WHERE MaCLB = :MaCLB AND MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'VaiTroCLB' => $data['VaiTroCLB'] ?? '',
            'NgayThamGia' => $data['NgayThamGia'] ?? date('Y-m-d H:i:s'),
            'MaCLB' => $maCLB,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function deleteClubMember(string $maCLB, string $maThanhVien): void
    {
        $stmt = $this->db->prepare('DELETE FROM ThanhVienCLB WHERE MaCLB = :MaCLB AND MaThanhVien = :MaThanhVien');
        $stmt->execute(['MaCLB' => $maCLB, 'MaThanhVien' => $maThanhVien]);
    }

    public function listEvents(?string $assistantId = null): array
    {
        $sql = $this->eventSelectSql();
        $params = [];
        if ($assistantId) {
            $sql .= ' WHERE ' . $this->assistantEventWhere();
            $params = ['assistantOwner' => $assistantId, 'assistantClubMember' => $assistantId];
        }
        $sql .= ' ORDER BY SuKien.NgayBatDau DESC, SuKien.MaSuKien ASC';
        return $this->fetchAll($sql, $params);
    }

    public function searchEvents(string $maSuKien, string $tenSuKien, string $maCLB = '', string $maLoaiSuKien = '', string $hocKy = '', string $namHoc = '', ?string $assistantId = null): array
    {
        $where = [
            "(:ma = '' OR SuKien.MaSuKien LIKE :maLike)",
            "(:ten = '' OR SuKien.TenSuKien LIKE :tenLike)",
            "(:maCLB = '' OR SuKien.MaCLB = :maCLBValue)",
            "(:maLoaiSuKien = '' OR SuKien.MaLoaiSuKien = :maLoaiSuKienValue)",
            "(:hocKy = '' OR SuKien.HocKy = :hocKyValue)",
            "(:namHoc = '' OR SuKien.NamHoc = :namHocValue)",
        ];
        $params = [
            'ma' => $maSuKien,
            'maLike' => '%' . $maSuKien . '%',
            'ten' => $tenSuKien,
            'tenLike' => '%' . $tenSuKien . '%',
            'maCLB' => $maCLB,
            'maCLBValue' => $maCLB,
            'maLoaiSuKien' => $maLoaiSuKien,
            'maLoaiSuKienValue' => $maLoaiSuKien,
            'hocKy' => $hocKy,
            'hocKyValue' => $hocKy,
            'namHoc' => $namHoc,
            'namHocValue' => $namHoc,
        ];
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        return $this->fetchAll($this->eventSelectSql() . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY SuKien.NgayBatDau DESC, SuKien.MaSuKien ASC', $params);
    }

    public function findEvent(string $maSuKien): ?array
    {
        return $this->fetchOne($this->eventSelectSql() . ' WHERE SuKien.MaSuKien = :MaSuKien LIMIT 1', ['MaSuKien' => $maSuKien]);
    }

    public function createEvent(array $data): void
    {
        if (empty($data['CheckinMoLuc']) && !empty($data['NgayBatDau'])) {
            $data['CheckinMoLuc'] = $data['NgayBatDau'];
        }
        if (empty($data['CheckinDongLuc']) && !empty($data['NgayKetThuc'])) {
            $data['CheckinDongLuc'] = $data['NgayKetThuc'];
        }
        $stmt = $this->db->prepare('INSERT INTO SuKien (MaSuKien, TenSuKien, MaCLB, MaLoaiSuKien, HocKy, NamHoc, MoTa, NgayBatDau, NgayKetThuc, NguoiToChuc, SucChua, CheckinMoLuc, CheckinDongLuc) VALUES (:MaSuKien, :TenSuKien, :MaCLB, :MaLoaiSuKien, :HocKy, :NamHoc, :MoTa, :NgayBatDau, :NgayKetThuc, :NguoiToChuc, :SucChua, :CheckinMoLuc, :CheckinDongLuc)');
        $stmt->execute([
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'TenSuKien' => $data['TenSuKien'] ?? '',
            'MaCLB' => $data['MaCLB'] ?? '',
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'NgayBatDau' => $data['NgayBatDau'] ?? date('Y-m-d H:i:s'),
            'NgayKetThuc' => $data['NgayKetThuc'] ?? date('Y-m-d H:i:s'),
            'NguoiToChuc' => $data['NguoiToChuc'] ?? '',
            'SucChua' => $data['SucChua'] ?? 1,
            'CheckinMoLuc' => $data['CheckinMoLuc'] ?? date('Y-m-d H:i:s'),
            'CheckinDongLuc' => $data['CheckinDongLuc'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateEvent(string $maSuKien, array $data): void
    {
        if (empty($data['CheckinMoLuc']) && !empty($data['NgayBatDau'])) {
            $data['CheckinMoLuc'] = $data['NgayBatDau'];
        }
        if (empty($data['CheckinDongLuc']) && !empty($data['NgayKetThuc'])) {
            $data['CheckinDongLuc'] = $data['NgayKetThuc'];
        }
        $stmt = $this->db->prepare('UPDATE SuKien SET TenSuKien = :TenSuKien, MaCLB = :MaCLB, MaLoaiSuKien = :MaLoaiSuKien, HocKy = :HocKy, NamHoc = :NamHoc, MoTa = :MoTa, NgayBatDau = :NgayBatDau, NgayKetThuc = :NgayKetThuc, NguoiToChuc = :NguoiToChuc, SucChua = :SucChua, CheckinMoLuc = :CheckinMoLuc, CheckinDongLuc = :CheckinDongLuc WHERE MaSuKien = :MaSuKien');
        $stmt->execute([
            'TenSuKien' => $data['TenSuKien'] ?? '',
            'MaCLB' => $data['MaCLB'] ?? '',
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'NgayBatDau' => $data['NgayBatDau'] ?? date('Y-m-d H:i:s'),
            'NgayKetThuc' => $data['NgayKetThuc'] ?? date('Y-m-d H:i:s'),
            'NguoiToChuc' => $data['NguoiToChuc'] ?? '',
            'SucChua' => $data['SucChua'] ?? 1,
            'CheckinMoLuc' => $data['CheckinMoLuc'] ?? date('Y-m-d H:i:s'),
            'CheckinDongLuc' => $data['CheckinDongLuc'] ?? date('Y-m-d H:i:s'),
            'MaSuKien' => $maSuKien,
        ]);
    }

    public function deleteEvent(string $maSuKien): void
    {
        $stmt = $this->db->prepare('DELETE FROM SuKien WHERE MaSuKien = :MaSuKien');
        $stmt->execute(['MaSuKien' => $maSuKien]);
    }

    public function listStudyGroups(): array
    {
        return $this->fetchAll($this->studyGroupSelectSql() . ' ORDER BY NhomHocTap.MaNhom ASC');
    }

    public function findStudyGroup(string $maNhom): ?array
    {
        return $this->fetchOne($this->studyGroupSelectSql() . ' WHERE NhomHocTap.MaNhom = :MaNhom LIMIT 1', ['MaNhom' => $maNhom]);
    }

    public function createStudyGroup(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO NhomHocTap (MaNhom, TenNhom, TroGiang, NgayTao, MoTa) VALUES (:MaNhom, :TenNhom, :TroGiang, :NgayTao, :MoTa)');
        $stmt->execute([
            'MaNhom' => $data['MaNhom'] ?? '',
            'TenNhom' => $data['TenNhom'] ?? '',
            'TroGiang' => $data['TroGiang'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MoTa' => $data['MoTa'] ?? '',
        ]);
    }

    public function updateStudyGroup(string $maNhom, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE NhomHocTap SET TenNhom = :TenNhom, TroGiang = :TroGiang, NgayTao = :NgayTao, MoTa = :MoTa WHERE MaNhom = :MaNhom');
        $stmt->execute([
            'TenNhom' => $data['TenNhom'] ?? '',
            'TroGiang' => $data['TroGiang'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MoTa' => $data['MoTa'] ?? '',
            'MaNhom' => $maNhom,
        ]);
    }

    public function deleteStudyGroup(string $maNhom): void
    {
        $stmt = $this->db->prepare('DELETE FROM NhomHocTap WHERE MaNhom = :MaNhom');
        $stmt->execute(['MaNhom' => $maNhom]);
    }

    public function listAttendance(?string $maThanhVien = null): array
    {
        $sql = $this->attendanceSelectSql();
        $params = [];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $sql .= ' WHERE DiemDanh.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        $sql .= ' ORDER BY DiemDanh.NgayDiemDanh DESC, DiemDanh.MaDiemDanh DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findAttendance($maDiemDanh, ?string $maThanhVien = null): ?array
    {
        $sql = $this->attendanceSelectSql() . ' WHERE DiemDanh.MaDiemDanh = :maDiemDanh';
        $params = ['maDiemDanh' => $maDiemDanh];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $sql .= ' AND DiemDanh.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        return $this->fetchOne($sql . ' LIMIT 1', $params);
    }

    public function createAttendance(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO DiemDanh (MaNhom, MaThanhVien, NgayDiemDanh, TrangThai, GhiChu) VALUES (:MaNhom, :MaThanhVien, :NgayDiemDanh, :TrangThai, :GhiChu)');
        $stmt->execute([
            'MaNhom' => $data['MaNhom'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayDiemDanh' => $data['NgayDiemDanh'] ?? date('Y-m-d'),
            'TrangThai' => $data['TrangThai'] ?? '',
            'GhiChu' => $data['GhiChu'] ?? '',
        ]);
    }

    public function updateAttendance($maDiemDanh, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE DiemDanh SET MaNhom = :MaNhom, MaThanhVien = :MaThanhVien, NgayDiemDanh = :NgayDiemDanh, TrangThai = :TrangThai, GhiChu = :GhiChu WHERE MaDiemDanh = :MaDiemDanh');
        $stmt->execute([
            'MaNhom' => $data['MaNhom'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayDiemDanh' => $data['NgayDiemDanh'] ?? date('Y-m-d'),
            'TrangThai' => $data['TrangThai'] ?? '',
            'GhiChu' => $data['GhiChu'] ?? '',
            'MaDiemDanh' => $maDiemDanh,
        ]);
    }

    public function deleteAttendance($maDiemDanh): void
    {
        $stmt = $this->db->prepare('DELETE FROM DiemDanh WHERE MaDiemDanh = :MaDiemDanh');
        $stmt->execute(['MaDiemDanh' => $maDiemDanh]);
    }

    public function listPosts(): array
    {
        return $this->fetchAll($this->postSelectSql() . ' ORDER BY BaiDang.NgayTao DESC, BaiDang.MaBaiDang DESC');
    }

    public function findPost(string $maBaiDang): ?array
    {
        return $this->fetchOne($this->postSelectSql() . ' WHERE BaiDang.MaBaiDang = :MaBaiDang LIMIT 1', ['MaBaiDang' => $maBaiDang]);
    }

    public function createPost(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO BaiDang (MaBaiDang, TieuDe, Anh, NoiDung, TacGia, NgayTao) VALUES (:MaBaiDang, :TieuDe, :Anh, :NoiDung, :TacGia, :NgayTao)');
        $stmt->execute([
            'MaBaiDang' => $data['MaBaiDang'] ?? '',
            'TieuDe' => $data['TieuDe'] ?? '',
            'Anh' => $data['Anh'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'TacGia' => $data['TacGia'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updatePost(string $maBaiDang, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE BaiDang SET TieuDe = :TieuDe, Anh = :Anh, NoiDung = :NoiDung, TacGia = :TacGia, NgayTao = :NgayTao WHERE MaBaiDang = :MaBaiDang');
        $stmt->execute([
            'TieuDe' => $data['TieuDe'] ?? '',
            'Anh' => $data['Anh'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'TacGia' => $data['TacGia'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MaBaiDang' => $maBaiDang,
        ]);
    }

    public function deletePost(string $maBaiDang): void
    {
        $stmt = $this->db->prepare('DELETE FROM BaiDang WHERE MaBaiDang = :MaBaiDang');
        $stmt->execute(['MaBaiDang' => $maBaiDang]);
    }

    public function listEventRegistrations(?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $sql = $this->eventRegistrationSelectSql();
        $where = [];
        $params = [];
        if ($maThanhVien) {
            $where[] = 'ThanhVienSuKien.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ThanhVienSuKien.NgayDangKy DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findEventRegistration(string $maSuKien, string $maThanhVien): ?array
    {
        return $this->fetchOne($this->eventRegistrationSelectSql() . ' WHERE ThanhVienSuKien.MaSuKien = :MaSuKien AND ThanhVienSuKien.MaThanhVien = :MaThanhVien LIMIT 1', [
            'MaSuKien' => $maSuKien,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function createEventRegistration(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ThanhVienSuKien (MaSuKien, MaThanhVien, NgayDangKy, TrangThaiThamGia, NgayXacNhan, XacNhanBoi) VALUES (:MaSuKien, :MaThanhVien, :NgayDangKy, :TrangThaiThamGia, :NgayXacNhan, :XacNhanBoi)');
        $stmt->execute([
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayDangKy' => $data['NgayDangKy'] ?? date('Y-m-d H:i:s'),
            'TrangThaiThamGia' => $data['TrangThaiThamGia'] ?? 'Đã đăng ký',
            'NgayXacNhan' => $data['NgayXacNhan'] ?? null,
            'XacNhanBoi' => $data['XacNhanBoi'] ?? null,
        ]);
    }

    public function updateEventRegistration(string $maSuKien, string $maThanhVien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVienSuKien SET NgayDangKy = :NgayDangKy, TrangThaiThamGia = :TrangThaiThamGia, NgayXacNhan = :NgayXacNhan, XacNhanBoi = :XacNhanBoi WHERE MaSuKien = :MaSuKien AND MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'NgayDangKy' => $data['NgayDangKy'] ?? date('Y-m-d H:i:s'),
            'TrangThaiThamGia' => $data['TrangThaiThamGia'] ?? 'Đã đăng ký',
            'NgayXacNhan' => $data['NgayXacNhan'] ?? null,
            'XacNhanBoi' => $data['XacNhanBoi'] ?? null,
            'MaSuKien' => $maSuKien,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function deleteEventRegistration(string $maSuKien, string $maThanhVien): void
    {
        $stmt = $this->db->prepare('DELETE FROM ThanhVienSuKien WHERE MaSuKien = :MaSuKien AND MaThanhVien = :MaThanhVien');
        $stmt->execute(['MaSuKien' => $maSuKien, 'MaThanhVien' => $maThanhVien]);
    }

    public function listCheckins(?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $sql = $this->checkinSelectSql();
        $where = [];
        $params = [];
        if ($maThanhVien) {
            $where[] = 'CheckinSuKien.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY CheckinSuKien.ThoiGianCheckin DESC, CheckinSuKien.MaCheckin DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findCheckin($maCheckin): ?array
    {
        return $this->fetchOne($this->checkinSelectSql() . ' WHERE CheckinSuKien.MaCheckin = :MaCheckin LIMIT 1', ['MaCheckin' => $maCheckin]);
    }

    public function listTrainingRules(): array
    {
        return $this->fetchAll($this->trainingRuleSelectSql() . ' ORDER BY QuyTacDiemRenLuyen.NamHoc DESC, QuyTacDiemRenLuyen.HocKy ASC');
    }

    public function findTrainingRule($maQuyTac): ?array
    {
        return $this->fetchOne($this->trainingRuleSelectSql() . ' WHERE QuyTacDiemRenLuyen.MaQuyTac = :MaQuyTac LIMIT 1', ['MaQuyTac' => $maQuyTac]);
    }

    public function createTrainingRule(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO QuyTacDiemRenLuyen (MaLoaiSuKien, HocKy, NamHoc, Diem, GhiChu) VALUES (:MaLoaiSuKien, :HocKy, :NamHoc, :Diem, :GhiChu)');
        $stmt->execute([
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'Diem' => $data['Diem'] ?? 0,
            'GhiChu' => $data['GhiChu'] ?? '',
        ]);
    }

    public function updateTrainingRule($maQuyTac, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE QuyTacDiemRenLuyen SET MaLoaiSuKien = :MaLoaiSuKien, HocKy = :HocKy, NamHoc = :NamHoc, Diem = :Diem, GhiChu = :GhiChu WHERE MaQuyTac = :MaQuyTac');
        $stmt->execute([
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'Diem' => $data['Diem'] ?? 0,
            'GhiChu' => $data['GhiChu'] ?? '',
            'MaQuyTac' => $maQuyTac,
        ]);
    }

    public function deleteTrainingRule($maQuyTac): void
    {
        $stmt = $this->db->prepare('DELETE FROM QuyTacDiemRenLuyen WHERE MaQuyTac = :MaQuyTac');
        $stmt->execute(['MaQuyTac' => $maQuyTac]);
    }

    public function listPoints(?string $hocKy = null, ?string $namHoc = null, ?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $where = [];
        $params = [];
        if ($hocKy) {
            $where[] = 'DiemRenLuyen.HocKy = :hocKy';
            $params['hocKy'] = $hocKy;
        }
        if ($namHoc) {
            $where[] = 'DiemRenLuyen.NamHoc = :namHoc';
            $params['namHoc'] = $namHoc;
        }
        if ($maThanhVien) {
            $where[] = 'DiemRenLuyen.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        $sql = $this->pointSelectSql();
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY DiemRenLuyen.NgayCong DESC, DiemRenLuyen.MaDiem DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findPoint($maDiem): ?array
    {
        return $this->fetchOne($this->pointSelectSql() . ' WHERE DiemRenLuyen.MaDiem = :MaDiem LIMIT 1', ['MaDiem' => $maDiem]);
    }

    public function listPointTotals(?string $maThanhVien = null): array
    {
        $sql = $this->pointTotalSelectSql();
        $params = [];
        if ($maThanhVien) {
            $sql .= ' WHERE TongDiemRenLuyen.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        $sql .= ' ORDER BY TongDiemRenLuyen.NamHoc DESC, TongDiemRenLuyen.HocKy ASC, ThanhVien.MaThanhVien ASC';
        return $this->fetchAll($sql, $params);
    }

    public function findPointTotal($maTongDiem): ?array
    {
        return $this->fetchOne($this->pointTotalSelectSql() . ' WHERE TongDiemRenLuyen.MaTongDiem = :MaTongDiem LIMIT 1', ['MaTongDiem' => $maTongDiem]);
    }

    public function listCertificates(?string $maSuKien = null, ?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $where = [];
        $params = [];
        if ($maSuKien) {
            $where[] = 'ChungNhan.MaSuKien = :maSuKien';
            $params['maSuKien'] = $maSuKien;
        }
        if ($maThanhVien) {
            $where[] = 'ChungNhan.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        $sql = $this->certificateSelectSql();
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ChungNhan.NgayCap DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findCertificate(string $maChungNhan): ?array
    {
        return $this->fetchOne($this->certificateSelectSql() . ' WHERE ChungNhan.MaChungNhan = :MaChungNhan LIMIT 1', ['MaChungNhan' => $maChungNhan]);
    }

    public function createCertificate(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ChungNhan (MaChungNhan, MaSuKien, MaThanhVien, NgayCap, NoiDung, CapBoi) VALUES (:MaChungNhan, :MaSuKien, :MaThanhVien, :NgayCap, :NoiDung, :CapBoi)');
        $stmt->execute([
            'MaChungNhan' => $data['MaChungNhan'] ?? '',
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayCap' => $data['NgayCap'] ?? date('Y-m-d H:i:s'),
            'NoiDung' => $data['NoiDung'] ?? '',
            'CapBoi' => $data['CapBoi'] ?? null,
        ]);
    }

    public function updateCertificate(string $maChungNhan, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ChungNhan SET MaSuKien = :MaSuKien, MaThanhVien = :MaThanhVien, NgayCap = :NgayCap, NoiDung = :NoiDung, CapBoi = :CapBoi WHERE MaChungNhan = :MaChungNhan');
        $stmt->execute([
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayCap' => $data['NgayCap'] ?? date('Y-m-d H:i:s'),
            'NoiDung' => $data['NoiDung'] ?? '',
            'CapBoi' => $data['CapBoi'] ?? null,
            'MaChungNhan' => $maChungNhan,
        ]);
    }

    public function deleteCertificate(string $maChungNhan): void
    {
        $stmt = $this->db->prepare('DELETE FROM ChungNhan WHERE MaChungNhan = :MaChungNhan');
        $stmt->execute(['MaChungNhan' => $maChungNhan]);
    }

    public function listReports(): array
    {
        return $this->fetchAll($this->reportSelectSql() . ' ORDER BY BaoCao.NgayNop DESC');
    }

    public function findReport($maBaoCao): ?array
    {
        return $this->fetchOne($this->reportSelectSql() . ' WHERE BaoCao.MaBaoCao = :MaBaoCao LIMIT 1', ['MaBaoCao' => $maBaoCao]);
    }

    public function createReport(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO BaoCao (TieuDe, NoiDung, NopBoi, NgayNop) VALUES (:TieuDe, :NoiDung, :NopBoi, :NgayNop)');
        $stmt->execute([
            'TieuDe' => $data['TieuDe'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'NopBoi' => $data['NopBoi'] ?? '',
            'NgayNop' => $data['NgayNop'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateReport($maBaoCao, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE BaoCao SET TieuDe = :TieuDe, NoiDung = :NoiDung, NopBoi = :NopBoi, NgayNop = :NgayNop WHERE MaBaoCao = :MaBaoCao');
        $stmt->execute([
            'TieuDe' => $data['TieuDe'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'NopBoi' => $data['NopBoi'] ?? '',
            'NgayNop' => $data['NgayNop'] ?? date('Y-m-d H:i:s'),
            'MaBaoCao' => $maBaoCao,
        ]);
    }

    public function deleteReport($maBaoCao): void
    {
        $stmt = $this->db->prepare('DELETE FROM BaoCao WHERE MaBaoCao = :MaBaoCao');
        $stmt->execute(['MaBaoCao' => $maBaoCao]);
    }

    public function registerEvent(string $maSuKien, string $maThanhVien): array
    {
        $this->db->beginTransaction();
        try {
            $eventStmt = $this->db->prepare('SELECT * FROM SuKien WHERE MaSuKien = :maSuKien FOR UPDATE');
            $eventStmt->execute(['maSuKien' => $maSuKien]);
            $event = $eventStmt->fetch();
            if (!$event) {
                throw new InvalidArgumentException('Không tìm thấy sự kiện.');
            }

            $existingStmt = $this->db->prepare('SELECT * FROM ThanhVienSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien FOR UPDATE');
            $existingStmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $existing = $existingStmt->fetch();
            if ($existing && ($existing['TrangThaiThamGia'] ?? '') !== 'Đã hủy') {
                $this->db->commit();
                return ['status' => 'exists', 'message' => 'Bạn đã đăng ký sự kiện này.'];
            }

            $countStmt = $this->db->prepare("SELECT COUNT(*) FROM ThanhVienSuKien WHERE MaSuKien = :maSuKien AND TrangThaiThamGia <> 'Đã hủy'");
            $countStmt->execute(['maSuKien' => $maSuKien]);
            $registered = (int)$countStmt->fetchColumn();
            if ($registered >= (int)$event['SucChua']) {
                throw new InvalidArgumentException('Sự kiện đã đủ số lượng đăng ký.');
            }

            if ($existing) {
                $update = $this->db->prepare("UPDATE ThanhVienSuKien SET TrangThaiThamGia = 'Đã đăng ký', NgayDangKy = NOW(), NgayXacNhan = NULL, XacNhanBoi = NULL WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien");
                $update->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
                $this->db->commit();
                return ['status' => 'restored', 'message' => 'Đăng ký lại sự kiện thành công.'];
            }

            $stmt = $this->db->prepare("INSERT INTO ThanhVienSuKien (MaSuKien, MaThanhVien, TrangThaiThamGia) VALUES (:maSuKien, :maThanhVien, 'Đã đăng ký')");
            $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $this->db->commit();
            return ['status' => 'created', 'message' => 'Đăng ký sự kiện thành công.'];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function cancelEventRegistration(string $maSuKien, string $maThanhVien): array
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT * FROM ThanhVienSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien FOR UPDATE');
            $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $registration = $stmt->fetch();
            if (!$registration) {
                throw new InvalidArgumentException('Bạn chưa đăng ký sự kiện này.');
            }
            if (($registration['TrangThaiThamGia'] ?? '') === 'Đã tham gia') {
                throw new InvalidArgumentException('Không thể hủy vì bạn đã check-in/tham gia sự kiện.');
            }
            if (($registration['TrangThaiThamGia'] ?? '') === 'Đã hủy') {
                $this->db->commit();
                return ['status' => 'exists', 'message' => 'Đăng ký này đã được hủy trước đó.'];
            }
            $checkStmt = $this->db->prepare('SELECT MaCheckin FROM CheckinSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien LIMIT 1');
            $checkStmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            if ($checkStmt->fetch()) {
                throw new InvalidArgumentException('Không thể hủy vì bạn đã check-in sự kiện.');
            }
            $update = $this->db->prepare("UPDATE ThanhVienSuKien SET TrangThaiThamGia = 'Đã hủy', NgayXacNhan = NULL, XacNhanBoi = NULL WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien");
            $update->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $this->db->commit();
            return ['status' => 'cancelled', 'message' => 'Đã hủy đăng ký sự kiện.'];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function confirmAttendance(string $maSuKien, string $maThanhVien, string $xacNhanBoi, string $phuongThuc = 'Thủ công'): array
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT tvsk.*, sk.TenSuKien, sk.MaLoaiSuKien, sk.HocKy, sk.NamHoc, tv.HoTen
                FROM ThanhVienSuKien tvsk
                INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
                INNER JOIN ThanhVien tv ON tv.MaThanhVien = tvsk.MaThanhVien
                WHERE tvsk.MaSuKien = :maSuKien AND tvsk.MaThanhVien = :maThanhVien
                FOR UPDATE');
            $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $registration = $stmt->fetch();
            if (!$registration) {
                throw new InvalidArgumentException('Sinh viên chưa đăng ký sự kiện này.');
            }
            if (($registration['TrangThaiThamGia'] ?? '') === 'Đã hủy') {
                throw new InvalidArgumentException('Đăng ký này đã bị hủy, không thể xác nhận tham gia.');
            }

            $ruleStmt = $this->db->prepare('SELECT * FROM QuyTacDiemRenLuyen WHERE MaLoaiSuKien = :maLoaiSuKien AND HocKy = :hocKy AND NamHoc = :namHoc LIMIT 1');
            $ruleStmt->execute([
                'maLoaiSuKien' => $registration['MaLoaiSuKien'],
                'hocKy' => $registration['HocKy'],
                'namHoc' => $registration['NamHoc'],
            ]);
            $rule = $ruleStmt->fetch();
            if (!$rule) {
                throw new InvalidArgumentException('Chưa cấu hình điểm rèn luyện cho loại sự kiện/học kỳ/năm học này.');
            }

            $update = $this->db->prepare("UPDATE ThanhVienSuKien SET TrangThaiThamGia = 'Đã tham gia', NgayXacNhan = NOW(), XacNhanBoi = :xacNhanBoi WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien");
            $update->execute(['xacNhanBoi' => $xacNhanBoi, 'maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);

            $checkin = $this->db->prepare('INSERT IGNORE INTO CheckinSuKien (MaSuKien, MaThanhVien, PhuongThuc, XacNhanBoi) VALUES (:maSuKien, :maThanhVien, :phuongThuc, :xacNhanBoi)');
            $checkin->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien, 'phuongThuc' => $phuongThuc, 'xacNhanBoi' => $xacNhanBoi]);

            $point = $this->db->prepare("INSERT INTO DiemRenLuyen (MaThanhVien, MaSuKien, MaQuyTac, HocKy, NamHoc, SoDiem, GhiChu) VALUES (:maThanhVien, :maSuKien, :maQuyTac, :hocKy, :namHoc, :soDiem, :ghiChu) ON DUPLICATE KEY UPDATE MaQuyTac = VALUES(MaQuyTac), HocKy = VALUES(HocKy), NamHoc = VALUES(NamHoc), SoDiem = VALUES(SoDiem), GhiChu = VALUES(GhiChu)");
            $point->execute([
                'maThanhVien' => $maThanhVien,
                'maSuKien' => $maSuKien,
                'maQuyTac' => $rule['MaQuyTac'],
                'hocKy' => $registration['HocKy'],
                'namHoc' => $registration['NamHoc'],
                'soDiem' => $rule['Diem'],
                'ghiChu' => 'Cộng tự động khi xác nhận tham gia sự kiện.',
            ]);

            $total = $this->db->prepare('INSERT INTO TongDiemRenLuyen (MaThanhVien, HocKy, NamHoc, TongDiem)
                SELECT :maThanhVien, :hocKy, :namHoc, COALESCE(SUM(SoDiem), 0)
                FROM DiemRenLuyen
                WHERE MaThanhVien = :maThanhVien2 AND HocKy = :hocKy2 AND NamHoc = :namHoc2
                ON DUPLICATE KEY UPDATE TongDiem = VALUES(TongDiem), CapNhatLuc = CURRENT_TIMESTAMP');
            $total->execute([
                'maThanhVien' => $maThanhVien,
                'hocKy' => $registration['HocKy'],
                'namHoc' => $registration['NamHoc'],
                'maThanhVien2' => $maThanhVien,
                'hocKy2' => $registration['HocKy'],
                'namHoc2' => $registration['NamHoc'],
            ]);

            $maChungNhan = 'CN-' . $maSuKien . '-' . $maThanhVien;
            $certificate = $this->db->prepare('INSERT IGNORE INTO ChungNhan (MaChungNhan, MaSuKien, MaThanhVien, NoiDung, CapBoi) VALUES (:maChungNhan, :maSuKien, :maThanhVien, :noiDung, :capBoi)');
            $certificate->execute([
                'maChungNhan' => $maChungNhan,
                'maSuKien' => $maSuKien,
                'maThanhVien' => $maThanhVien,
                'noiDung' => 'Chứng nhận đã tham gia sự kiện ' . $registration['TenSuKien'],
                'capBoi' => $xacNhanBoi,
            ]);

            $this->db->commit();
            return [
                'MaSuKien' => $maSuKien,
                'MaThanhVien' => $maThanhVien,
                'TrangThaiThamGia' => 'Đã tham gia',
                'SoDiem' => (float)$rule['Diem'],
                'MaChungNhan' => $maChungNhan,
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function checkInEvent(string $maSuKien, string $maThanhVien, string $token): array
    {
        $event = $this->findEvent($maSuKien);
        if (!$event) {
            throw new InvalidArgumentException('Không tìm thấy sự kiện.');
        }
        if (!hash_equals((string)($event['CheckinToken'] ?? ''), $token)) {
            throw new InvalidArgumentException('Mã QR check-in không hợp lệ.');
        }
        $now = time();
        $openAt = strtotime((string)($event['CheckinMoLuc'] ?? ''));
        $closeAt = strtotime((string)($event['CheckinDongLuc'] ?? ''));
        if ($openAt !== false && $now < $openAt) {
            throw new InvalidArgumentException('QR check-in chưa đến thời gian hiệu lực. Vui lòng quay lại từ ' . date('d/m/Y H:i', $openAt) . '.');
        }
        if ($closeAt !== false && $now > $closeAt) {
            throw new InvalidArgumentException('QR check-in đã hết hiệu lực lúc ' . date('d/m/Y H:i', $closeAt) . '.');
        }
        $exists = $this->db->prepare('SELECT MaCheckin FROM CheckinSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien LIMIT 1');
        $exists->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
        if ($exists->fetch()) {
            throw new InvalidArgumentException('Bạn đã check-in sự kiện này trước đó.');
        }
        return $this->confirmAttendance($maSuKien, $maThanhVien, $maThanhVien, 'QR');
    }

    public function ensureEventToken(string $maSuKien): string
    {
        $event = $this->findEvent($maSuKien);
        if (!$event) {
            throw new InvalidArgumentException('Không tìm thấy sự kiện.');
        }
        if (!empty($event['CheckinToken'])) {
            return (string)$event['CheckinToken'];
        }
        $token = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare('UPDATE SuKien SET CheckinToken = :token WHERE MaSuKien = :maSuKien');
        $stmt->execute(['token' => $token, 'maSuKien' => $maSuKien]);
        return $token;
    }

    public function registrationsForEvent(string $maSuKien): array
    {
        return $this->fetchAll($this->eventRegistrationSelectSql() . ' WHERE ThanhVienSuKien.MaSuKien = :maSuKien ORDER BY ThanhVienSuKien.NgayDangKy DESC', ['maSuKien' => $maSuKien]);
    }

    public function syncTrainingPointsFromRules(): array
    {
        $this->db->beginTransaction();
        try {
            $missingSql = "SELECT sk.MaLoaiSuKien, COALESCE(lsk.TenLoaiSuKien, sk.MaLoaiSuKien) AS TenLoaiSuKien, sk.HocKy, sk.NamHoc, GROUP_CONCAT(DISTINCT sk.MaSuKien ORDER BY sk.MaSuKien SEPARATOR ', ') AS SuKienThieu
                FROM ThanhVienSuKien tvsk
                INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
                LEFT JOIN LoaiSuKien lsk ON lsk.MaLoaiSuKien = sk.MaLoaiSuKien
                LEFT JOIN QuyTacDiemRenLuyen qt ON qt.MaLoaiSuKien = sk.MaLoaiSuKien AND qt.HocKy = sk.HocKy AND qt.NamHoc = sk.NamHoc
                WHERE tvsk.TrangThaiThamGia = 'Đã tham gia' AND qt.MaQuyTac IS NULL
                GROUP BY sk.MaLoaiSuKien, TenLoaiSuKien, sk.HocKy, sk.NamHoc
                ORDER BY sk.NamHoc DESC, sk.HocKy ASC, sk.MaLoaiSuKien ASC";
            $missing = $this->db->query($missingSql)->fetchAll();
            if ($missing) {
                $parts = array_map(static function (array $row): string {
                    return sprintf('%s (%s, %s, sự kiện: %s)', $row['TenLoaiSuKien'], $row['HocKy'], $row['NamHoc'], $row['SuKienThieu']);
                }, $missing);
                throw new InvalidArgumentException('Chưa cấu hình điểm rèn luyện cho: ' . implode('; ', $parts) . '.');
            }

            $this->db->exec('DELETE FROM TongDiemRenLuyen');
            $this->db->exec('DELETE FROM DiemRenLuyen');

            $points = $this->db->prepare("INSERT INTO DiemRenLuyen (MaThanhVien, MaSuKien, MaQuyTac, HocKy, NamHoc, SoDiem, GhiChu)
                SELECT tvsk.MaThanhVien, tvsk.MaSuKien, qt.MaQuyTac, sk.HocKy, sk.NamHoc, qt.Diem, 'Đồng bộ tự động từ quy tắc điểm.'
                FROM ThanhVienSuKien tvsk
                INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
                INNER JOIN QuyTacDiemRenLuyen qt ON qt.MaLoaiSuKien = sk.MaLoaiSuKien AND qt.HocKy = sk.HocKy AND qt.NamHoc = sk.NamHoc
                WHERE tvsk.TrangThaiThamGia = 'Đã tham gia'");
            $points->execute();
            $pointRows = $points->rowCount();

            $totals = $this->db->prepare('INSERT INTO TongDiemRenLuyen (MaThanhVien, HocKy, NamHoc, TongDiem)
                SELECT MaThanhVien, HocKy, NamHoc, SUM(SoDiem)
                FROM DiemRenLuyen
                GROUP BY MaThanhVien, HocKy, NamHoc');
            $totals->execute();
            $totalRows = $totals->rowCount();

            $this->db->commit();
            return [
                'SoDongDiem' => $pointRows,
                'SoDongTong' => $totalRows,
                'message' => 'Đã đồng bộ điểm rèn luyện từ quy tắc điểm.',
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function termPointTotals(string $hocKy, string $namHoc, ?string $maCLB = null): array
    {
        if ($maCLB) {
            return $this->fetchAll('SELECT DiemRenLuyen.MaThanhVien, ThanhVien.HoTen, ThanhVien.Email, DiemRenLuyen.HocKy, DiemRenLuyen.NamHoc, SUM(DiemRenLuyen.SoDiem) AS TongDiem, MAX(DiemRenLuyen.NgayCong) AS CapNhatLuc
                FROM DiemRenLuyen
                INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = DiemRenLuyen.MaThanhVien
                INNER JOIN SuKien ON SuKien.MaSuKien = DiemRenLuyen.MaSuKien
                WHERE DiemRenLuyen.HocKy = :hocKy AND DiemRenLuyen.NamHoc = :namHoc AND SuKien.MaCLB = :maCLB
                GROUP BY DiemRenLuyen.MaThanhVien, ThanhVien.HoTen, ThanhVien.Email, DiemRenLuyen.HocKy, DiemRenLuyen.NamHoc
                ORDER BY DiemRenLuyen.MaThanhVien ASC', ['hocKy' => $hocKy, 'namHoc' => $namHoc, 'maCLB' => $maCLB]);
        }
        return $this->fetchAll('SELECT TongDiemRenLuyen.*, ThanhVien.HoTen, ThanhVien.Email
            FROM TongDiemRenLuyen
            INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = TongDiemRenLuyen.MaThanhVien
            WHERE TongDiemRenLuyen.HocKy = :hocKy AND TongDiemRenLuyen.NamHoc = :namHoc
            ORDER BY ThanhVien.MaThanhVien ASC', ['hocKy' => $hocKy, 'namHoc' => $namHoc]);
    }

    public function updatePassword(string $maThanhVien, string $oldPassword, string $newPassword): void
    {
        $member = $this->findMember($maThanhVien);
        if (!$member || (string)$member['MatKhau'] !== $oldPassword) {
            throw new InvalidArgumentException('Mật khẩu cũ không đúng.');
        }
        $stmt = $this->db->prepare('UPDATE ThanhVien SET MatKhau = :matKhau WHERE MaThanhVien = :maThanhVien');
        $stmt->execute(['matKhau' => $newPassword, 'maThanhVien' => $maThanhVien]);
    }

    public function dashboardStats(?string $hocKy = null, ?string $namHoc = null, ?string $maCLB = null): array
    {
        $eventWhere = [];
        $params = [];
        if ($hocKy) {
            $eventWhere[] = 'SuKien.HocKy = :hocKy';
            $params['hocKy'] = $hocKy;
        }
        if ($namHoc) {
            $eventWhere[] = 'SuKien.NamHoc = :namHoc';
            $params['namHoc'] = $namHoc;
        }
        if ($maCLB) {
            $eventWhere[] = 'SuKien.MaCLB = :maCLB';
            $params['maCLB'] = $maCLB;
        }
        $eventCondition = $eventWhere ? ' WHERE ' . implode(' AND ', $eventWhere) : '';
        $joinEventCondition = $eventWhere ? ' AND ' . implode(' AND ', $eventWhere) : '';

        $events = $this->db->prepare('SELECT COUNT(*) FROM SuKien' . $eventCondition);
        $events->execute($params);
        $registrations = $this->db->prepare("SELECT COUNT(*) FROM ThanhVienSuKien INNER JOIN SuKien ON SuKien.MaSuKien = ThanhVienSuKien.MaSuKien" . ($eventWhere ? ' WHERE ' . implode(' AND ', $eventWhere) . " AND ThanhVienSuKien.TrangThaiThamGia <> 'Đã hủy'" : " WHERE ThanhVienSuKien.TrangThaiThamGia <> 'Đã hủy'"));
        $registrations->execute($params);
        $checkins = $this->db->prepare('SELECT COUNT(*) FROM CheckinSuKien INNER JOIN SuKien ON SuKien.MaSuKien = CheckinSuKien.MaSuKien' . $eventCondition);
        $checkins->execute($params);
        $points = $this->db->prepare('SELECT COALESCE(SUM(DiemRenLuyen.SoDiem), 0) FROM DiemRenLuyen INNER JOIN SuKien ON SuKien.MaSuKien = DiemRenLuyen.MaSuKien' . $eventCondition);
        $points->execute($params);

        $byClub = $this->db->prepare("SELECT CLB.MaCLB, CLB.TenCLB, COUNT(DISTINCT SuKien.MaSuKien) AS SoSuKien, COUNT(DISTINCT CheckinSuKien.MaCheckin) AS SoCheckin, COALESCE(SUM(DISTINCT DiemRenLuyen.SoDiem), 0) AS TongDiem FROM CLB LEFT JOIN SuKien ON SuKien.MaCLB = CLB.MaCLB" . $joinEventCondition . ' LEFT JOIN CheckinSuKien ON CheckinSuKien.MaSuKien = SuKien.MaSuKien LEFT JOIN DiemRenLuyen ON DiemRenLuyen.MaSuKien = SuKien.MaSuKien GROUP BY CLB.MaCLB, CLB.TenCLB ORDER BY CLB.TenCLB ASC');
        $byClub->execute($params);

        $top = $this->db->prepare("SELECT ThanhVien.MaThanhVien, ThanhVien.HoTen, COUNT(DISTINCT CheckinSuKien.MaCheckin) AS SoLuotThamGia, COALESCE(SUM(DiemRenLuyen.SoDiem), 0) AS TongDiem FROM ThanhVien LEFT JOIN CheckinSuKien ON CheckinSuKien.MaThanhVien = ThanhVien.MaThanhVien LEFT JOIN SuKien ON SuKien.MaSuKien = CheckinSuKien.MaSuKien LEFT JOIN DiemRenLuyen ON DiemRenLuyen.MaThanhVien = ThanhVien.MaThanhVien AND DiemRenLuyen.MaSuKien = SuKien.MaSuKien WHERE ThanhVien.MaVaiTro = 'TV'" . ($eventWhere ? ' AND ' . implode(' AND ', $eventWhere) : '') . ' GROUP BY ThanhVien.MaThanhVien, ThanhVien.HoTen ORDER BY TongDiem DESC, SoLuotThamGia DESC, ThanhVien.MaThanhVien ASC LIMIT 10');
        $top->execute($params);

        return [
            'summary' => [
                'SoSuKien' => (int)$events->fetchColumn(),
                'SoDangKy' => (int)$registrations->fetchColumn(),
                'SoCheckin' => (int)$checkins->fetchColumn(),
                'TongDiem' => (float)$points->fetchColumn(),
            ],
            'byClub' => $byClub->fetchAll(),
            'topStudents' => $top->fetchAll(),
        ];
    }

    public function canManageClub(string $maCLB, string $maThanhVien): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM CLB WHERE MaCLB = :maCLB AND ChuNhiem = :maThanhVien UNION SELECT 1 FROM ThanhVienCLB WHERE MaCLB = :maCLB2 AND MaThanhVien = :maThanhVien2 AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức') LIMIT 1");
        $stmt->execute([
            'maCLB' => $maCLB,
            'maThanhVien' => $maThanhVien,
            'maCLB2' => $maCLB,
            'maThanhVien2' => $maThanhVien,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public function canManageEvent(string $maSuKien, string $maThanhVien): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM SuKien WHERE MaSuKien = :maSuKien AND (NguoiToChuc = :owner OR MaCLB IN (SELECT MaCLB FROM ThanhVienCLB WHERE MaThanhVien = :member AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức'))) LIMIT 1");
        $stmt->execute(['maSuKien' => $maSuKien, 'owner' => $maThanhVien, 'member' => $maThanhVien]);
        return (bool)$stmt->fetchColumn();
    }

    private function clubSelectSql(): string
    {
        return 'SELECT CLB.*, ThanhVien.HoTen AS ChuNhiemTen
            FROM CLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = CLB.ChuNhiem';
    }

    private function clubMemberSelectSql(): string
    {
        return 'SELECT ThanhVienCLB.*, CLB.TenCLB, ThanhVien.HoTen, ThanhVien.Email
            FROM ThanhVienCLB
            LEFT JOIN CLB ON CLB.MaCLB = ThanhVienCLB.MaCLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = ThanhVienCLB.MaThanhVien';
    }

    private function eventSelectSql(): string
    {
        return "SELECT SuKien.*, CLB.TenCLB, ThanhVien.HoTen AS NguoiToChucTen, LoaiSuKien.TenLoaiSuKien,
                (SELECT COUNT(*) FROM ThanhVienSuKien tvsk WHERE tvsk.MaSuKien = SuKien.MaSuKien AND tvsk.TrangThaiThamGia <> 'Đã hủy') AS SoDangKy,
                (SELECT COUNT(*) FROM CheckinSuKien ck WHERE ck.MaSuKien = SuKien.MaSuKien) AS SoCheckin,
                GREATEST(SuKien.SucChua - (SELECT COUNT(*) FROM ThanhVienSuKien tvsk2 WHERE tvsk2.MaSuKien = SuKien.MaSuKien AND tvsk2.TrangThaiThamGia <> 'Đã hủy'), 0) AS SoChoConLai
            FROM SuKien
            LEFT JOIN CLB ON CLB.MaCLB = SuKien.MaCLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = SuKien.NguoiToChuc
            LEFT JOIN LoaiSuKien ON LoaiSuKien.MaLoaiSuKien = SuKien.MaLoaiSuKien";
    }

    private function studyGroupSelectSql(): string
    {
        return 'SELECT NhomHocTap.*, ThanhVien.HoTen AS TroGiangTen
            FROM NhomHocTap
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = NhomHocTap.TroGiang';
    }

    private function attendanceSelectSql(): string
    {
        return 'SELECT DiemDanh.*, NhomHocTap.TenNhom, ThanhVien.HoTen
            FROM DiemDanh
            LEFT JOIN NhomHocTap ON NhomHocTap.MaNhom = DiemDanh.MaNhom
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = DiemDanh.MaThanhVien';
    }

    private function postSelectSql(): string
    {
        return 'SELECT BaiDang.*, ThanhVien.HoTen AS TacGiaTen
            FROM BaiDang
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = BaiDang.TacGia';
    }

    private function eventRegistrationSelectSql(): string
    {
        return 'SELECT ThanhVienSuKien.*, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, ThanhVien.HoTen, NguoiXacNhan.HoTen AS XacNhanBoiTen
            FROM ThanhVienSuKien
            LEFT JOIN SuKien ON SuKien.MaSuKien = ThanhVienSuKien.MaSuKien
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = ThanhVienSuKien.MaThanhVien
            LEFT JOIN ThanhVien NguoiXacNhan ON NguoiXacNhan.MaThanhVien = ThanhVienSuKien.XacNhanBoi';
    }

    private function checkinSelectSql(): string
    {
        return 'SELECT CheckinSuKien.*, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, CLB.TenCLB, ThanhVien.HoTen, NguoiXacNhan.HoTen AS XacNhanBoiTen
            FROM CheckinSuKien
            LEFT JOIN SuKien ON SuKien.MaSuKien = CheckinSuKien.MaSuKien
            LEFT JOIN CLB ON CLB.MaCLB = SuKien.MaCLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = CheckinSuKien.MaThanhVien
            LEFT JOIN ThanhVien NguoiXacNhan ON NguoiXacNhan.MaThanhVien = CheckinSuKien.XacNhanBoi';
    }

    private function trainingRuleSelectSql(): string
    {
        return 'SELECT QuyTacDiemRenLuyen.*, LoaiSuKien.TenLoaiSuKien
            FROM QuyTacDiemRenLuyen
            LEFT JOIN LoaiSuKien ON LoaiSuKien.MaLoaiSuKien = QuyTacDiemRenLuyen.MaLoaiSuKien';
    }

    private function pointSelectSql(): string
    {
        return 'SELECT DiemRenLuyen.*, ThanhVien.HoTen, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, QuyTacDiemRenLuyen.Diem AS DiemQuyTac
            FROM DiemRenLuyen
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = DiemRenLuyen.MaThanhVien
            LEFT JOIN SuKien ON SuKien.MaSuKien = DiemRenLuyen.MaSuKien
            LEFT JOIN QuyTacDiemRenLuyen ON QuyTacDiemRenLuyen.MaQuyTac = DiemRenLuyen.MaQuyTac';
    }

    private function pointTotalSelectSql(): string
    {
        return 'SELECT TongDiemRenLuyen.*, ThanhVien.HoTen
            FROM TongDiemRenLuyen
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = TongDiemRenLuyen.MaThanhVien';
    }

    private function certificateSelectSql(): string
    {
        return 'SELECT ChungNhan.*, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, ThanhVien.HoTen, NguoiCap.HoTen AS CapBoiTen
            FROM ChungNhan
            LEFT JOIN SuKien ON SuKien.MaSuKien = ChungNhan.MaSuKien
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = ChungNhan.MaThanhVien
            LEFT JOIN ThanhVien NguoiCap ON NguoiCap.MaThanhVien = ChungNhan.CapBoi';
    }

    private function reportSelectSql(): string
    {
        return 'SELECT BaoCao.*, ThanhVien.HoTen AS NopBoiTen
            FROM BaoCao
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = BaoCao.NopBoi';
    }

    private function assistantClubSubquery(): string
    {
        return "SELECT MaCLB FROM ThanhVienCLB WHERE MaThanhVien = :assistantClubMember AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức')";
    }

    private function assistantClubWhere(): string
    {
        return '(CLB.ChuNhiem = :assistantOwner OR CLB.MaCLB IN (' . $this->assistantClubSubquery() . '))';
    }

    private function assistantEventWhere(): string
    {
        return '(SuKien.NguoiToChuc = :assistantOwner OR SuKien.MaCLB IN (' . $this->assistantClubSubquery() . '))';
    }
}
