<?php
class Repository
{
    private PDO $db;
    private array $resources;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->resources = require APP_PATH . '/config/resources.php';
    }

    public function config(string $resource): array
    {
        if (!isset($this->resources[$resource])) {
            throw new InvalidArgumentException('Unknown resource: ' . $resource);
        }
        return $this->resources[$resource];
    }

    public function all(string $resource): array
    {
        $cfg = $this->config($resource);
        $sql = $this->selectSql($cfg) . (!empty($cfg['order']) ? ' ORDER BY ' . $cfg['order'] : '');
        return $this->db->query($sql)->fetchAll();
    }

    public function allForMember(string $resource, string $maThanhVien): array
    {
        $cfg = $this->config($resource);
        $sql = $this->selectSql($cfg) . ' WHERE ' . $cfg['table'] . '.MaThanhVien = :maThanhVien';
        if (!empty($cfg['order'])) {
            $sql .= ' ORDER BY ' . $cfg['order'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['maThanhVien' => $maThanhVien]);
        return $stmt->fetchAll();
    }

    public function find(string $resource, array $keys): ?array
    {
        $cfg = $this->config($resource);
        $where = $this->whereForPk($cfg, $keys);
        $stmt = $this->db->prepare($this->selectSql($cfg) . ' WHERE ' . $where['sql'] . ' LIMIT 1');
        $stmt->execute($where['params']);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(string $resource, array $data): void
    {
        $cfg = $this->config($resource);
        foreach (($cfg['auto'] ?? []) as $auto) {
            if (($data[$auto] ?? '') === '') {
                unset($data[$auto]);
            }
        }
        $columns = array_keys($data);
        $sql = 'INSERT INTO ' . $cfg['table'] . ' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    public function update(string $resource, array $keys, array $data): void
    {
        $cfg = $this->config($resource);
        foreach (array_merge($cfg['pk'], $cfg['auto'] ?? []) as $skip) {
            unset($data[$skip]);
        }
        $sets = [];
        $params = [];
        foreach ($data as $column => $value) {
            $sets[] = $column . ' = :set_' . $column;
            $params['set_' . $column] = $value;
        }
        $where = $this->whereForPk($cfg, $keys, 'pk_');
        $stmt = $this->db->prepare('UPDATE ' . $cfg['table'] . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where['sql']);
        $stmt->execute(array_merge($params, $where['params']));
    }

    public function delete(string $resource, array $keys): void
    {
        $cfg = $this->config($resource);
        $where = $this->whereForPk($cfg, $keys);
        $stmt = $this->db->prepare('DELETE FROM ' . $cfg['table'] . ' WHERE ' . $where['sql']);
        $stmt->execute($where['params']);
    }

    public function options(array $relation): array
    {
        $sql = sprintf('SELECT %s AS value, %s AS label FROM %s ORDER BY %s', $relation['value'], $relation['label'], $relation['table'], $relation['label']);
        return $this->db->query($sql)->fetchAll();
    }

    public function searchMembers(string $maThanhVien, string $hoTen): array
    {
        $stmt = $this->db->prepare("SELECT ThanhVien.*, VaiTro.TenVaiTro FROM ThanhVien LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro WHERE (:ma = '' OR ThanhVien.MaThanhVien LIKE :maLike) AND (:ten = '' OR ThanhVien.HoTen LIKE :tenLike) ORDER BY ThanhVien.MaThanhVien ASC");
        $stmt->execute(['ma' => $maThanhVien, 'maLike' => '%' . $maThanhVien . '%', 'ten' => $hoTen, 'tenLike' => '%' . $hoTen . '%']);
        return $stmt->fetchAll();
    }

    public function searchEvents(string $maSuKien, string $tenSuKien): array
    {
        $stmt = $this->db->prepare("SELECT SuKien.*, ThanhVien.HoTen AS NguoiToChucTen, LoaiSuKien.TenLoaiSuKien FROM SuKien LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = SuKien.NguoiToChuc LEFT JOIN LoaiSuKien ON LoaiSuKien.MaLoaiSuKien = SuKien.MaLoaiSuKien WHERE (:ma = '' OR SuKien.MaSuKien LIKE :maLike) AND (:ten = '' OR SuKien.TenSuKien LIKE :tenLike) ORDER BY SuKien.NgayBatDau DESC, SuKien.MaSuKien ASC");
        $stmt->execute(['ma' => $maSuKien, 'maLike' => '%' . $maSuKien . '%', 'ten' => $tenSuKien, 'tenLike' => '%' . $tenSuKien . '%']);
        return $stmt->fetchAll();
    }

    public function findMemberByEmail(?string $email): ?array
    {
        if (!$email) {
            return null;
        }
        $stmt = $this->db->prepare('SELECT ThanhVien.*, VaiTro.TenVaiTro FROM ThanhVien LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro WHERE ThanhVien.Email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function login(string $email, string $password): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ThanhVien WHERE Email = :email AND MatKhau = :password LIMIT 1');
        $stmt->execute(['email' => $email, 'password' => $password]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function registerEvent(string $maSuKien, string $maThanhVien): array
    {
        $event = $this->find('SuKien', ['MaSuKien' => $maSuKien]);
        if (!$event) {
            throw new InvalidArgumentException('Không tìm thấy sự kiện.');
        }
        $existing = $this->find('ThanhVienSuKien', ['MaSuKien' => $maSuKien, 'MaThanhVien' => $maThanhVien]);
        if ($existing) {
            return ['status' => 'exists', 'message' => 'Bạn đã đăng ký sự kiện này.'];
        }
        $stmt = $this->db->prepare("INSERT INTO ThanhVienSuKien (MaSuKien, MaThanhVien, TrangThaiThamGia) VALUES (:maSuKien, :maThanhVien, 'Đã đăng ký')");
        $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
        return ['status' => 'created', 'message' => 'Đăng ký sự kiện thành công.'];
    }

    public function confirmAttendance(string $maSuKien, string $maThanhVien, string $xacNhanBoi): array
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT tvsk.*, sk.TenSuKien, sk.MaLoaiSuKien, sk.HocKy, sk.NamHoc, tv.HoTen FROM ThanhVienSuKien tvsk INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien INNER JOIN ThanhVien tv ON tv.MaThanhVien = tvsk.MaThanhVien WHERE tvsk.MaSuKien = :maSuKien AND tvsk.MaThanhVien = :maThanhVien FOR UPDATE");
            $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $registration = $stmt->fetch();
            if (!$registration) {
                throw new InvalidArgumentException('Sinh viên chưa đăng ký sự kiện này.');
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

            $total = $this->db->prepare("INSERT INTO TongDiemRenLuyen (MaThanhVien, HocKy, NamHoc, TongDiem) SELECT :maThanhVien, :hocKy, :namHoc, COALESCE(SUM(SoDiem), 0) FROM DiemRenLuyen WHERE MaThanhVien = :maThanhVien2 AND HocKy = :hocKy2 AND NamHoc = :namHoc2 ON DUPLICATE KEY UPDATE TongDiem = VALUES(TongDiem), CapNhatLuc = CURRENT_TIMESTAMP");
            $total->execute([
                'maThanhVien' => $maThanhVien,
                'hocKy' => $registration['HocKy'],
                'namHoc' => $registration['NamHoc'],
                'maThanhVien2' => $maThanhVien,
                'hocKy2' => $registration['HocKy'],
                'namHoc2' => $registration['NamHoc'],
            ]);

            $maChungNhan = 'CN-' . $maSuKien . '-' . $maThanhVien;
            $certificate = $this->db->prepare("INSERT IGNORE INTO ChungNhan (MaChungNhan, MaSuKien, MaThanhVien, NoiDung, CapBoi) VALUES (:maChungNhan, :maSuKien, :maThanhVien, :noiDung, :capBoi)");
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

    public function listPoints(?string $hocKy = null, ?string $namHoc = null, ?string $maThanhVien = null): array
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
        $sql = $this->selectSql($this->config('DiemRenLuyen'));
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY DiemRenLuyen.NgayCong DESC, DiemRenLuyen.MaDiem DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listCertificates(?string $maSuKien = null, ?string $maThanhVien = null): array
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
        $sql = $this->selectSql($this->config('ChungNhan'));
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ChungNhan.NgayCap DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function termPointTotals(string $hocKy, string $namHoc): array
    {
        $stmt = $this->db->prepare("SELECT TongDiemRenLuyen.*, ThanhVien.HoTen, ThanhVien.Email FROM TongDiemRenLuyen INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = TongDiemRenLuyen.MaThanhVien WHERE TongDiemRenLuyen.HocKy = :hocKy AND TongDiemRenLuyen.NamHoc = :namHoc ORDER BY ThanhVien.MaThanhVien ASC");
        $stmt->execute(['hocKy' => $hocKy, 'namHoc' => $namHoc]);
        return $stmt->fetchAll();
    }

    private function selectSql(array $cfg): string
    {
        return 'SELECT ' . ($cfg['select'] ?? ($cfg['table'] . '.*')) . ' FROM ' . $cfg['table'] . (!empty($cfg['join']) ? ' ' . $cfg['join'] : '');
    }

    private function whereForPk(array $cfg, array $keys, string $prefix = ''): array
    {
        $parts = [];
        $params = [];
        foreach ($cfg['pk'] as $pk) {
            if (!array_key_exists($pk, $keys)) {
                throw new InvalidArgumentException('Missing primary key: ' . $pk);
            }
            $param = $prefix . $pk;
            $parts[] = $cfg['table'] . '.' . $pk . ' = :' . $param;
            $params[$param] = $keys[$pk];
        }
        return ['sql' => implode(' AND ', $parts), 'params' => $params];
    }
}
