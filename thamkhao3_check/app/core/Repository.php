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

    public function allForAssistantScope(string $resource, string $maThanhVien): array
    {
        $cfg = $this->config($resource);
        $scope = $this->assistantScope($resource, $maThanhVien);
        if (!$scope) {
            return $this->all($resource);
        }
        $sql = $this->selectSql($cfg) . ' WHERE ' . $scope['sql'];
        if (!empty($cfg['order'])) {
            $sql .= ' ORDER BY ' . $cfg['order'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($scope['params']);
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
        if ($resource === 'SuKien' && empty($data['CheckinToken'])) {
            $data['CheckinToken'] = bin2hex(random_bytes(16));
        }
        if ($resource === 'SuKien') {
            if (empty($data['CheckinMoLuc']) && !empty($data['NgayBatDau'])) {
                $data['CheckinMoLuc'] = $data['NgayBatDau'];
            }
            if (empty($data['CheckinDongLuc']) && !empty($data['NgayKetThuc'])) {
                $data['CheckinDongLuc'] = $data['NgayKetThuc'];
            }
        }
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
        if ($resource === 'SuKien' && empty($data['CheckinToken'])) {
            unset($data['CheckinToken']);
        }
        if ($resource === 'SuKien') {
            if (empty($data['CheckinMoLuc']) && !empty($data['NgayBatDau'])) {
                $data['CheckinMoLuc'] = $data['NgayBatDau'];
            }
            if (empty($data['CheckinDongLuc']) && !empty($data['NgayKetThuc'])) {
                $data['CheckinDongLuc'] = $data['NgayKetThuc'];
            }
        }
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

    public function searchEvents(string $maSuKien, string $tenSuKien, string $maCLB = '', string $maLoaiSuKien = '', string $hocKy = '', string $namHoc = '', ?string $assistantId = null): array
    {
        $cfg = $this->config('SuKien');
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
            $scope = $this->assistantScope('SuKien', $assistantId);
            if ($scope) {
                $where[] = $scope['sql'];
                $params = array_merge($params, $scope['params']);
            }
        }
        $sql = $this->selectSql($cfg) . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $cfg['order'];
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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
            $stmt = $this->db->prepare("SELECT tvsk.*, sk.TenSuKien, sk.MaLoaiSuKien, sk.HocKy, sk.NamHoc, tv.HoTen FROM ThanhVienSuKien tvsk INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien INNER JOIN ThanhVien tv ON tv.MaThanhVien = tvsk.MaThanhVien WHERE tvsk.MaSuKien = :maSuKien AND tvsk.MaThanhVien = :maThanhVien FOR UPDATE");
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

    public function checkInEvent(string $maSuKien, string $maThanhVien, string $token): array
    {
        $event = $this->find('SuKien', ['MaSuKien' => $maSuKien]);
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
        $event = $this->find('SuKien', ['MaSuKien' => $maSuKien]);
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
        $sql = $this->selectSql($this->config('ThanhVienSuKien')) . ' WHERE ThanhVienSuKien.MaSuKien = :maSuKien ORDER BY ThanhVienSuKien.NgayDangKy DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['maSuKien' => $maSuKien]);
        return $stmt->fetchAll();
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

    public function termPointTotals(string $hocKy, string $namHoc, ?string $maCLB = null): array
    {
        if ($maCLB) {
            $stmt = $this->db->prepare("SELECT DiemRenLuyen.MaThanhVien, ThanhVien.HoTen, ThanhVien.Email, DiemRenLuyen.HocKy, DiemRenLuyen.NamHoc, SUM(DiemRenLuyen.SoDiem) AS TongDiem, MAX(DiemRenLuyen.NgayCong) AS CapNhatLuc FROM DiemRenLuyen INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = DiemRenLuyen.MaThanhVien INNER JOIN SuKien ON SuKien.MaSuKien = DiemRenLuyen.MaSuKien WHERE DiemRenLuyen.HocKy = :hocKy AND DiemRenLuyen.NamHoc = :namHoc AND SuKien.MaCLB = :maCLB GROUP BY DiemRenLuyen.MaThanhVien, ThanhVien.HoTen, ThanhVien.Email, DiemRenLuyen.HocKy, DiemRenLuyen.NamHoc ORDER BY DiemRenLuyen.MaThanhVien ASC");
            $stmt->execute(['hocKy' => $hocKy, 'namHoc' => $namHoc, 'maCLB' => $maCLB]);
            return $stmt->fetchAll();
        }
        $stmt = $this->db->prepare("SELECT TongDiemRenLuyen.*, ThanhVien.HoTen, ThanhVien.Email FROM TongDiemRenLuyen INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = TongDiemRenLuyen.MaThanhVien WHERE TongDiemRenLuyen.HocKy = :hocKy AND TongDiemRenLuyen.NamHoc = :namHoc ORDER BY ThanhVien.MaThanhVien ASC");
        $stmt->execute(['hocKy' => $hocKy, 'namHoc' => $namHoc]);
        return $stmt->fetchAll();
    }

    public function updatePassword(string $maThanhVien, string $oldPassword, string $newPassword): void
    {
        $member = $this->find('ThanhVien', ['MaThanhVien' => $maThanhVien]);
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

        $byClub = $this->db->prepare("SELECT CLB.MaCLB, CLB.TenCLB, COUNT(DISTINCT SuKien.MaSuKien) AS SoSuKien, COUNT(DISTINCT CheckinSuKien.MaCheckin) AS SoCheckin, COALESCE(SUM(DISTINCT DiemRenLuyen.SoDiem), 0) AS TongDiem FROM CLB LEFT JOIN SuKien ON SuKien.MaCLB = CLB.MaCLB" . $joinEventCondition . " LEFT JOIN CheckinSuKien ON CheckinSuKien.MaSuKien = SuKien.MaSuKien LEFT JOIN DiemRenLuyen ON DiemRenLuyen.MaSuKien = SuKien.MaSuKien GROUP BY CLB.MaCLB, CLB.TenCLB ORDER BY CLB.TenCLB ASC");
        $byClub->execute($params);

        $top = $this->db->prepare("SELECT ThanhVien.MaThanhVien, ThanhVien.HoTen, COUNT(DISTINCT CheckinSuKien.MaCheckin) AS SoLuotThamGia, COALESCE(SUM(DiemRenLuyen.SoDiem), 0) AS TongDiem FROM ThanhVien LEFT JOIN CheckinSuKien ON CheckinSuKien.MaThanhVien = ThanhVien.MaThanhVien LEFT JOIN SuKien ON SuKien.MaSuKien = CheckinSuKien.MaSuKien LEFT JOIN DiemRenLuyen ON DiemRenLuyen.MaThanhVien = ThanhVien.MaThanhVien AND DiemRenLuyen.MaSuKien = SuKien.MaSuKien WHERE ThanhVien.MaVaiTro = 'TV'" . ($eventWhere ? ' AND ' . implode(' AND ', $eventWhere) : '') . " GROUP BY ThanhVien.MaThanhVien, ThanhVien.HoTen ORDER BY TongDiem DESC, SoLuotThamGia DESC, ThanhVien.MaThanhVien ASC LIMIT 10");
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

    private function assistantScope(string $resource, string $maThanhVien): ?array
    {
        $clubScope = "SELECT MaCLB FROM ThanhVienCLB WHERE MaThanhVien = :assistantClubMember AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức')";
        if ($resource === 'CLB') {
            return [
                'sql' => "(CLB.ChuNhiem = :assistantOwner OR CLB.MaCLB IN ($clubScope))",
                'params' => ['assistantOwner' => $maThanhVien, 'assistantClubMember' => $maThanhVien],
            ];
        }
        if ($resource === 'ThanhVienCLB') {
            return [
                'sql' => "ThanhVienCLB.MaCLB IN ($clubScope)",
                'params' => ['assistantClubMember' => $maThanhVien],
            ];
        }
        if ($resource === 'SuKien') {
            return [
                'sql' => "(SuKien.NguoiToChuc = :assistantOwner OR SuKien.MaCLB IN ($clubScope))",
                'params' => ['assistantOwner' => $maThanhVien, 'assistantClubMember' => $maThanhVien],
            ];
        }
        if (in_array($resource, ['ThanhVienSuKien', 'CheckinSuKien', 'DiemRenLuyen', 'ChungNhan'], true)) {
            return [
                'sql' => "(SuKien.NguoiToChuc = :assistantOwner OR SuKien.MaCLB IN ($clubScope))",
                'params' => ['assistantOwner' => $maThanhVien, 'assistantClubMember' => $maThanhVien],
            ];
        }
        return null;
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
