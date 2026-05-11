<?php
class Api_64131060Controller extends Controller
{
    public function DangKySuKien(): void
    {
        try {
            $this->requireRoles(['TV']);
            $maSuKien = trim($_GET['MaSuKien'] ?? '');
            if ($maSuKien === '') {
                throw new InvalidArgumentException('Thiếu mã sự kiện.');
            }
            $result = $this->repo()->registerEvent($maSuKien, $this->memberId());
            $this->json(['success' => true, 'message' => $result['message'], 'data' => $result]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function XacNhanThamGia(): void
    {
        try {
            $this->requireRoles(['TVCN', 'TVTG']);
            if (!$this->isPost()) {
                throw new InvalidArgumentException('Phương thức không hợp lệ.');
            }
            $maSuKien = trim($_POST['MaSuKien'] ?? '');
            $maThanhVien = trim($_POST['MaThanhVien'] ?? '');
            if ($maSuKien === '' || $maThanhVien === '') {
                throw new InvalidArgumentException('Thiếu mã sự kiện hoặc mã thành viên.');
            }
            $data = $this->repo()->confirmAttendance($maSuKien, $maThanhVien, $this->memberId());
            $this->json([
                'success' => true,
                'message' => 'Đã xác nhận tham gia, cộng điểm rèn luyện và cấp chứng nhận.',
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function DiemRenLuyen(): void
    {
        try {
            $this->requireLogin();
            $hocKy = trim($_GET['HocKy'] ?? '') ?: null;
            $namHoc = trim($_GET['NamHoc'] ?? '') ?: null;
            $maThanhVien = current_role() === 'TV' ? $this->memberId() : (trim($_GET['MaThanhVien'] ?? '') ?: null);
            $rows = $this->repo()->listPoints($hocKy, $namHoc, $maThanhVien);
            $this->json(['success' => true, 'data' => $rows]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function ChungNhan(): void
    {
        try {
            $this->requireLogin();
            $maSuKien = trim($_GET['MaSuKien'] ?? '') ?: null;
            $maThanhVien = current_role() === 'TV' ? $this->memberId() : (trim($_GET['MaThanhVien'] ?? '') ?: null);
            $rows = $this->repo()->listCertificates($maSuKien, $maThanhVien);
            $this->json(['success' => true, 'data' => $rows]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function memberId(): string
    {
        if (current_member_id()) {
            return (string)current_member_id();
        }
        $member = $this->repo()->findMemberByEmail(current_email());
        if (!$member) {
            throw new RuntimeException('Không tìm thấy tài khoản hiện tại.');
        }
        $_SESSION['MaThanhVien'] = $member['MaThanhVien'];
        return $member['MaThanhVien'];
    }
}
