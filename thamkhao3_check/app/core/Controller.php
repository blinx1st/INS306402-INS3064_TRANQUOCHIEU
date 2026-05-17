<?php
class Controller
{
    protected ?Repository $repo = null;

    public function __construct()
    {
    }

    protected function repo(): Repository
    {
        if ($this->repo === null) {
            $this->repo = new Repository();
        }
        return $this->repo;
    }

    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . h($view);
            return;
        }
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (is_file($layoutFile)) {
            require $layoutFile;
            return;
        }
        echo $content;
    }

    protected function notFound(string $message = 'Không tìm thấy dữ liệu.'): void
    {
        http_response_code(404);
        $this->render('generic/message', ['title' => 'Không tìm thấy', 'message' => $message]);
    }

    protected function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function requireLogin(): void
    {
        if (!current_role()) {
            if ($this->wantsJson()) {
                $this->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
            }
            redirect_to('Login_64131060', 'Login_64131060');
        }
    }

    protected function requireRoles(array $roles): void
    {
        $this->requireLogin();
        if (!in_array((string)current_role(), $roles, true)) {
            $this->denyUnauthorized();
        }
    }

    protected function denyUnauthorized(): void
    {
        if ($this->wantsJson()) {
            $this->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }
        http_response_code(403);
        $home = current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : 'MemberPage_64131060');
        $this->render('generic/message', [
            'title' => 'Không có quyền',
            'message' => 'Bạn không có quyền truy cập chức năng này.',
            'buttonText' => 'QUAY VỀ',
            'buttonUrl' => url_for('TrangChu_64131060', $home),
        ]);
        exit;
    }

    protected function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function resourceCfg(string $resource): array
    {
        $configs = [
            'ThanhVien' => [
                'table' => 'ThanhVien',
                'pk' => ['MaThanhVien'],
                'title' => 'Thành viên',
                'fields' => [
                    'MaThanhVien' => ['label' => 'Mã số', 'type' => 'text', 'required' => true, 'max_length' => 50],
                    'HoTen' => ['label' => 'Họ tên', 'type' => 'text', 'required' => true, 'max_length' => 100],
                    'Email' => ['label' => 'Email', 'type' => 'email', 'required' => true, 'max_length' => 100],
                    'MatKhau' => ['label' => 'Mật khẩu', 'type' => 'text', 'required' => true, 'max_length' => 255],
                    'MaVaiTro' => ['label' => 'Vai trò', 'type' => 'select', 'relation' => ['table' => 'VaiTro', 'value' => 'MaVaiTro', 'label' => 'TenVaiTro'], 'required' => true],
                    'NgayTao' => ['label' => 'Ngày tạo', 'type' => 'datetime'],
                ],
                'list' => ['MaThanhVien' => 'Mã số', 'HoTen' => 'Họ tên', 'Email' => 'Email', 'MatKhau' => 'Mật khẩu', 'TenVaiTro' => 'Vai trò', 'NgayTao' => 'Ngày tạo'],
            ],
            'CLB' => [
                'table' => 'CLB',
                'pk' => ['MaCLB'],
                'title' => 'Câu lạc bộ',
                'fields' => [
                    'MaCLB' => ['label' => 'Mã CLB', 'type' => 'text', 'required' => true, 'max_length' => 50],
                    'TenCLB' => ['label' => 'Tên CLB', 'type' => 'text', 'required' => true, 'max_length' => 150],
                    'MoTa' => ['label' => 'Mô tả', 'type' => 'textarea'],
                    'ChuNhiem' => ['label' => 'Chủ nhiệm', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'nullable' => true],
                    'NgayThanhLap' => ['label' => 'Ngày thành lập', 'type' => 'date', 'nullable' => true],
                ],
                'list' => ['MaCLB' => 'Mã CLB', 'TenCLB' => 'Tên CLB', 'ChuNhiemTen' => 'Chủ nhiệm', 'NgayThanhLap' => 'Ngày thành lập', 'MoTa' => 'Mô tả'],
            ],
            'ThanhVienCLB' => [
                'table' => 'ThanhVienCLB',
                'pk' => ['MaCLB', 'MaThanhVien'],
                'title' => 'Thành viên CLB',
                'fields' => [
                    'MaCLB' => ['label' => 'CLB', 'type' => 'select', 'relation' => ['table' => 'CLB', 'value' => 'MaCLB', 'label' => 'TenCLB'], 'required' => true],
                    'MaThanhVien' => ['label' => 'Thành viên', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'VaiTroCLB' => ['label' => 'Vai trò trong CLB', 'type' => 'select_static', 'options' => ['Chủ nhiệm' => 'Chủ nhiệm', 'Ban tổ chức' => 'Ban tổ chức', 'Thành viên' => 'Thành viên'], 'required' => true],
                    'NgayThamGia' => ['label' => 'Ngày tham gia', 'type' => 'datetime'],
                ],
                'list' => ['TenCLB' => 'CLB', 'HoTen' => 'Thành viên', 'Email' => 'Email', 'VaiTroCLB' => 'Vai trò CLB', 'NgayThamGia' => 'Ngày tham gia'],
            ],
            'SuKien' => [
                'table' => 'SuKien',
                'pk' => ['MaSuKien'],
                'title' => 'Sự kiện',
                'fields' => [
                    'MaSuKien' => ['label' => 'Mã sự kiện', 'type' => 'text', 'required' => true, 'max_length' => 50],
                    'TenSuKien' => ['label' => 'Tên sự kiện', 'type' => 'text', 'required' => true, 'max_length' => 100],
                    'MaCLB' => ['label' => 'CLB tổ chức', 'type' => 'select', 'relation' => ['table' => 'CLB', 'value' => 'MaCLB', 'label' => 'TenCLB'], 'required' => true],
                    'MaLoaiSuKien' => ['label' => 'Loại sự kiện', 'type' => 'select', 'relation' => ['table' => 'LoaiSuKien', 'value' => 'MaLoaiSuKien', 'label' => 'TenLoaiSuKien'], 'required' => true],
                    'HocKy' => ['label' => 'Học kỳ', 'type' => 'select_static', 'options' => ['HK1' => 'HK1', 'HK2' => 'HK2', 'HK3' => 'HK3'], 'required' => true],
                    'NamHoc' => ['label' => 'Năm học', 'type' => 'text', 'required' => true, 'pattern' => '/^\d{4}-\d{4}$/'],
                    'MoTa' => ['label' => 'Mô tả', 'type' => 'textarea'],
                    'NgayBatDau' => ['label' => 'Ngày bắt đầu', 'type' => 'datetime', 'required' => true],
                    'NgayKetThuc' => ['label' => 'Ngày kết thúc', 'type' => 'datetime', 'required' => true],
                    'NguoiToChuc' => ['label' => 'Người tổ chức', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'SucChua' => ['label' => 'Sức chứa', 'type' => 'number', 'required' => true, 'min' => 1],
                    'CheckinMoLuc' => ['label' => 'QR mở lúc', 'type' => 'datetime', 'required' => true],
                    'CheckinDongLuc' => ['label' => 'QR đóng lúc', 'type' => 'datetime', 'required' => true],
                ],
                'list' => ['MaSuKien' => 'Mã sự kiện', 'TenSuKien' => 'Tên sự kiện', 'TenCLB' => 'CLB', 'TenLoaiSuKien' => 'Loại', 'HocKy' => 'Học kỳ', 'NamHoc' => 'Năm học', 'SucChua' => 'Sức chứa', 'SoDangKy' => 'Đã đăng ký', 'SoCheckin' => 'Đã check-in', 'SoChoConLai' => 'Còn chỗ', 'NgayBatDau' => 'Bắt đầu', 'NgayKetThuc' => 'Kết thúc', 'CheckinMoLuc' => 'QR mở', 'CheckinDongLuc' => 'QR đóng'],
            ],
            'NhomHocTap' => [
                'table' => 'NhomHocTap',
                'pk' => ['MaNhom'],
                'title' => 'Nhóm học tập',
                'fields' => [
                    'MaNhom' => ['label' => 'Mã nhóm', 'type' => 'text', 'required' => true, 'max_length' => 50],
                    'TenNhom' => ['label' => 'Tên nhóm', 'type' => 'text', 'required' => true, 'max_length' => 100],
                    'TroGiang' => ['label' => 'Trợ giảng', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'NgayTao' => ['label' => 'Ngày tạo', 'type' => 'datetime'],
                    'MoTa' => ['label' => 'Mô tả', 'type' => 'textarea'],
                ],
                'list' => ['MaNhom' => 'Mã nhóm', 'TenNhom' => 'Tên nhóm', 'TroGiangTen' => 'Trợ giảng', 'NgayTao' => 'Ngày tạo', 'MoTa' => 'Mô tả'],
            ],
            'BaiDang' => [
                'table' => 'BaiDang',
                'pk' => ['MaBaiDang'],
                'title' => 'Bài đăng',
                'fields' => [
                    'MaBaiDang' => ['label' => 'Mã bài đăng', 'type' => 'text', 'required' => true, 'max_length' => 50],
                    'TieuDe' => ['label' => 'Tiêu đề', 'type' => 'text', 'required' => true, 'max_length' => 255],
                    'Anh' => ['label' => 'Ảnh', 'type' => 'image', 'required' => true],
                    'NoiDung' => ['label' => 'Nội dung', 'type' => 'textarea', 'required' => true],
                    'TacGia' => ['label' => 'Tác giả', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'NgayTao' => ['label' => 'Ngày tạo', 'type' => 'datetime'],
                ],
                'list' => ['MaBaiDang' => 'Mã', 'TieuDe' => 'Tiêu đề', 'Anh' => 'Ảnh', 'NoiDung' => 'Nội dung', 'TacGiaTen' => 'Tác giả', 'NgayTao' => 'Ngày tạo'],
            ],
            'ThanhVienSuKien' => [
                'table' => 'ThanhVienSuKien',
                'pk' => ['MaSuKien', 'MaThanhVien'],
                'title' => 'Thành viên tham gia sự kiện',
                'ajaxConfirm' => true,
                'fields' => [
                    'MaSuKien' => ['label' => 'Sự kiện', 'type' => 'select', 'relation' => ['table' => 'SuKien', 'value' => 'MaSuKien', 'label' => 'TenSuKien'], 'required' => true],
                    'MaThanhVien' => ['label' => 'Thành viên', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'NgayDangKy' => ['label' => 'Ngày đăng ký', 'type' => 'datetime'],
                    'TrangThaiThamGia' => ['label' => 'Trạng thái tham gia', 'type' => 'select_static', 'options' => ['Đã đăng ký' => 'Đã đăng ký', 'Đã tham gia' => 'Đã tham gia', 'Vắng' => 'Vắng', 'Đã hủy' => 'Đã hủy'], 'required' => true],
                    'NgayXacNhan' => ['label' => 'Ngày xác nhận', 'type' => 'datetime', 'nullable' => true],
                    'XacNhanBoi' => ['label' => 'Xác nhận bởi', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'nullable' => true],
                ],
                'list' => ['TenSuKien' => 'Sự kiện', 'HoTen' => 'Thành viên', 'NgayDangKy' => 'Ngày đăng ký', 'TrangThaiThamGia' => 'Trạng thái', 'NgayXacNhan' => 'Ngày xác nhận', 'XacNhanBoiTen' => 'Xác nhận bởi'],
            ],
            'CheckinSuKien' => [
                'table' => 'CheckinSuKien',
                'pk' => ['MaCheckin'],
                'auto' => ['MaCheckin'],
                'title' => 'Log check-in sự kiện',
                'fields' => [
                    'MaCheckin' => ['label' => 'Mã check-in', 'type' => 'number', 'readonly' => true],
                    'MaSuKien' => ['label' => 'Sự kiện', 'type' => 'select', 'relation' => ['table' => 'SuKien', 'value' => 'MaSuKien', 'label' => 'TenSuKien'], 'required' => true],
                    'MaThanhVien' => ['label' => 'Thành viên', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'ThoiGianCheckin' => ['label' => 'Thời gian check-in', 'type' => 'datetime'],
                    'PhuongThuc' => ['label' => 'Phương thức', 'type' => 'select_static', 'options' => ['QR' => 'QR', 'Thủ công' => 'Thủ công'], 'required' => true],
                    'XacNhanBoi' => ['label' => 'Xác nhận bởi', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'nullable' => true],
                ],
                'list' => ['MaCheckin' => 'Mã', 'TenSuKien' => 'Sự kiện', 'TenCLB' => 'CLB', 'HoTen' => 'Sinh viên', 'ThoiGianCheckin' => 'Thời gian', 'PhuongThuc' => 'Phương thức', 'XacNhanBoiTen' => 'Xác nhận bởi'],
            ],
            'QuyTacDiemRenLuyen' => [
                'table' => 'QuyTacDiemRenLuyen',
                'pk' => ['MaQuyTac'],
                'auto' => ['MaQuyTac'],
                'title' => 'Quy tắc điểm rèn luyện',
                'fields' => [
                    'MaQuyTac' => ['label' => 'Mã quy tắc', 'type' => 'number', 'readonly' => true],
                    'MaLoaiSuKien' => ['label' => 'Loại sự kiện', 'type' => 'select', 'relation' => ['table' => 'LoaiSuKien', 'value' => 'MaLoaiSuKien', 'label' => 'TenLoaiSuKien'], 'required' => true],
                    'HocKy' => ['label' => 'Học kỳ', 'type' => 'select_static', 'options' => ['HK1' => 'HK1', 'HK2' => 'HK2', 'HK3' => 'HK3'], 'required' => true],
                    'NamHoc' => ['label' => 'Năm học', 'type' => 'text', 'required' => true, 'pattern' => '/^\d{4}-\d{4}$/'],
                    'Diem' => ['label' => 'Điểm', 'type' => 'number', 'required' => true, 'min' => 0],
                    'GhiChu' => ['label' => 'Ghi chú', 'type' => 'textarea'],
                ],
                'list' => ['MaQuyTac' => 'Mã', 'TenLoaiSuKien' => 'Loại sự kiện', 'HocKy' => 'Học kỳ', 'NamHoc' => 'Năm học', 'Diem' => 'Điểm', 'GhiChu' => 'Ghi chú'],
            ],
            'DiemRenLuyen' => [
                'table' => 'DiemRenLuyen',
                'pk' => ['MaDiem'],
                'auto' => ['MaDiem'],
                'title' => 'Điểm rèn luyện',
                'fields' => [
                    'MaDiem' => ['label' => 'Mã điểm', 'type' => 'number', 'readonly' => true],
                    'MaThanhVien' => ['label' => 'Thành viên', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'MaSuKien' => ['label' => 'Sự kiện', 'type' => 'select', 'relation' => ['table' => 'SuKien', 'value' => 'MaSuKien', 'label' => 'TenSuKien'], 'required' => true],
                    'MaQuyTac' => ['label' => 'Quy tắc điểm', 'type' => 'select', 'relation' => ['table' => 'QuyTacDiemRenLuyen', 'value' => 'MaQuyTac', 'label' => 'MaQuyTac'], 'required' => true],
                    'HocKy' => ['label' => 'Học kỳ', 'type' => 'select_static', 'options' => ['HK1' => 'HK1', 'HK2' => 'HK2', 'HK3' => 'HK3'], 'required' => true],
                    'NamHoc' => ['label' => 'Năm học', 'type' => 'text', 'required' => true, 'pattern' => '/^\d{4}-\d{4}$/'],
                    'SoDiem' => ['label' => 'Số điểm', 'type' => 'number', 'required' => true, 'min' => 0],
                    'NgayCong' => ['label' => 'Ngày cộng', 'type' => 'datetime'],
                    'GhiChu' => ['label' => 'Ghi chú', 'type' => 'textarea'],
                ],
                'list' => ['MaDiem' => 'Mã', 'HoTen' => 'Thành viên', 'TenSuKien' => 'Sự kiện', 'HocKy' => 'Học kỳ', 'NamHoc' => 'Năm học', 'SoDiem' => 'Điểm', 'NgayCong' => 'Ngày cộng', 'GhiChu' => 'Ghi chú'],
            ],
            'TongDiemRenLuyen' => [
                'table' => 'TongDiemRenLuyen',
                'pk' => ['MaTongDiem'],
                'auto' => ['MaTongDiem'],
                'title' => 'Tổng điểm rèn luyện',
                'fields' => [
                    'MaTongDiem' => ['label' => 'Mã tổng điểm', 'type' => 'number', 'readonly' => true],
                    'MaThanhVien' => ['label' => 'Thành viên', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'HocKy' => ['label' => 'Học kỳ', 'type' => 'select_static', 'options' => ['HK1' => 'HK1', 'HK2' => 'HK2', 'HK3' => 'HK3'], 'required' => true],
                    'NamHoc' => ['label' => 'Năm học', 'type' => 'text', 'required' => true, 'pattern' => '/^\d{4}-\d{4}$/'],
                    'TongDiem' => ['label' => 'Tổng điểm', 'type' => 'number', 'required' => true, 'min' => 0],
                    'CapNhatLuc' => ['label' => 'Cập nhật lúc', 'type' => 'datetime'],
                ],
                'list' => ['MaTongDiem' => 'Mã', 'HoTen' => 'Thành viên', 'HocKy' => 'Học kỳ', 'NamHoc' => 'Năm học', 'TongDiem' => 'Tổng điểm', 'CapNhatLuc' => 'Cập nhật lúc'],
            ],
            'ChungNhan' => [
                'table' => 'ChungNhan',
                'pk' => ['MaChungNhan'],
                'title' => 'Chứng nhận',
                'fields' => [
                    'MaChungNhan' => ['label' => 'Mã chứng nhận', 'type' => 'text', 'required' => true, 'max_length' => 100],
                    'MaSuKien' => ['label' => 'Sự kiện', 'type' => 'select', 'relation' => ['table' => 'SuKien', 'value' => 'MaSuKien', 'label' => 'TenSuKien'], 'required' => true],
                    'MaThanhVien' => ['label' => 'Thành viên', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'NgayCap' => ['label' => 'Ngày cấp', 'type' => 'datetime'],
                    'NoiDung' => ['label' => 'Nội dung', 'type' => 'textarea', 'required' => true],
                    'CapBoi' => ['label' => 'Cấp bởi', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'nullable' => true],
                ],
                'list' => ['MaChungNhan' => 'Mã chứng nhận', 'TenSuKien' => 'Sự kiện', 'HoTen' => 'Thành viên', 'NgayCap' => 'Ngày cấp', 'NoiDung' => 'Nội dung', 'CapBoiTen' => 'Cấp bởi'],
            ],
            'BaoCao' => [
                'table' => 'BaoCao',
                'pk' => ['MaBaoCao'],
                'auto' => ['MaBaoCao'],
                'title' => 'Báo cáo',
                'fields' => [
                    'MaBaoCao' => ['label' => 'Mã báo cáo', 'type' => 'number', 'readonly' => true],
                    'TieuDe' => ['label' => 'Tiêu đề', 'type' => 'text', 'required' => true, 'max_length' => 100],
                    'NoiDung' => ['label' => 'Nội dung', 'type' => 'textarea', 'required' => true],
                    'NopBoi' => ['label' => 'Nộp bởi', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                    'NgayNop' => ['label' => 'Ngày nộp', 'type' => 'datetime'],
                ],
                'list' => ['MaBaoCao' => 'Mã', 'TieuDe' => 'Tiêu đề', 'NoiDung' => 'Nội dung', 'NopBoiTen' => 'Nộp bởi', 'NgayNop' => 'Ngày nộp'],
            ],
        ];

        if (!isset($configs[$resource])) {
            throw new InvalidArgumentException('Unknown resource config: ' . $resource);
        }
        return $configs[$resource];
    }

    protected function renderCrudList(string $title, string $controller, string $listAction, array $cfg, array $rows, bool $canWrite, array $extra = []): void
    {
        $this->render('generic/list', $extra + [
            'title' => $title,
            'controller' => $controller,
            'listAction' => $listAction,
            'cfg' => $cfg,
            'rows' => $rows,
            'canWrite' => $canWrite,
        ]);
    }

    protected function renderCrudDetails(string $controller, string $listAction, array $cfg, array $row, array $keys, bool $canWrite, string $title = ''): void
    {
        $this->render('generic/details', [
            'title' => $title ?: 'Thông tin chi tiết ' . lower_text($cfg['title']),
            'controller' => $controller,
            'listAction' => $listAction,
            'cfg' => $cfg,
            'row' => $row,
            'keys' => $keys,
            'canWrite' => $canWrite,
        ]);
    }

    protected function renderCrudForm(string $controller, string $listAction, array $cfg, array $row, string $action, string $title, string $error = '', array $keys = [], bool $canWrite = true, array $relations = []): void
    {
        $this->render('generic/form', [
            'cfg' => $cfg,
            'row' => $row,
            'action' => $action,
            'title' => $title,
            'error' => $error,
            'keys' => $keys,
            'relations' => $relations ?: $this->relationsForCfg($cfg),
            'controller' => $controller,
            'listAction' => $listAction,
            'canWrite' => $canWrite,
        ]);
    }

    protected function renderCrudDelete(string $controller, string $listAction, array $cfg, array $row, array $keys, bool $canWrite, string $error = ''): void
    {
        $this->render('generic/delete', [
            'title' => 'Xóa ' . lower_text($cfg['title']),
            'controller' => $controller,
            'listAction' => $listAction,
            'cfg' => $cfg,
            'row' => $row,
            'keys' => $keys,
            'error' => $error,
            'canWrite' => $canWrite,
        ]);
    }

    protected function relationsForCfg(array $cfg): array
    {
        $relations = [];
        foreach ($cfg['fields'] as $field => $meta) {
            if (($meta['type'] ?? '') === 'select' && isset($meta['relation'])) {
                $relations[$field] = $this->repo()->options($meta['relation']);
            }
        }
        return $relations;
    }

    protected function collectResourceData(array $cfg, array $existing = []): array
    {
        $data = [];
        foreach ($cfg['fields'] as $field => $meta) {
            $type = $meta['type'] ?? 'text';
            if (($meta['readonly'] ?? false) && in_array($field, $cfg['auto'] ?? [], true)) {
                continue;
            }
            if ($type === 'image') {
                $data[$field] = $this->handleUpload($field, $existing[$field] ?? ($_POST[$field] ?? ''));
                continue;
            }
            $value = $_POST[$field] ?? '';
            if (($meta['nullable'] ?? false) && trim((string)$value) === '') {
                $data[$field] = null;
                continue;
            }
            if ($type === 'datetime') {
                $value = $value === '' ? date('Y-m-d H:i:s') : str_replace('T', ' ', $value);
                if (strlen((string)$value) === 16) {
                    $value .= ':00';
                }
            }
            if ($type === 'date' && $value === '') {
                $value = date('Y-m-d');
            }
            $data[$field] = $value;
        }
        return $data;
    }

    protected function keysFromRequest(array $cfg, array $params): array
    {
        $keys = [];
        foreach ($cfg['pk'] as $index => $pk) {
            if (isset($_POST[$pk])) {
                $keys[$pk] = $_POST[$pk];
            } elseif (isset($_GET[$pk])) {
                $keys[$pk] = $_GET[$pk];
            } elseif (isset($params[$index])) {
                $keys[$pk] = $params[$index];
            } elseif (isset($_GET['id']) && count($cfg['pk']) === 1) {
                $keys[$pk] = $_GET['id'];
            }
        }
        return $keys;
    }

    protected function currentMemberId(): string
    {
        if (current_member_id()) {
            return (string)current_member_id();
        }
        $member = $this->repo()->findMemberByEmail(current_email());
        if ($member) {
            return (string)$member['MaThanhVien'];
        }
        redirect_to('Login_64131060', 'Login_64131060');
    }

    protected function renderProfile(string $controller, string $editAction): void
    {
        $this->requireLogin();
        $member = $this->repo()->findMemberByEmail(current_email());
        if (!$member) {
            redirect_to('Login_64131060', 'Login_64131060');
        }
        $this->render('generic/profile', [
            'title' => 'Trang cá nhân',
            'controller' => $controller,
            'editAction' => $editAction,
            'cfg' => $this->resourceCfg('ThanhVien'),
            'row' => $member,
            'canWrite' => true,
        ]);
    }

    protected function renderAlert(string $title, string $message, string $buttonText, string $controller, string $action): void
    {
        $this->render('generic/message', [
            'title' => $title,
            'message' => $message,
            'buttonText' => $buttonText,
            'buttonUrl' => url_for($controller, $action),
        ]);
    }

    protected function renderGeneratedPointWriteBlocked(string $controller, string $listAction): void
    {
        $this->render('generic/message', [
            'title' => 'Không nhập điểm thủ công',
            'message' => 'Điểm rèn luyện được tự động tính từ quy tắc điểm khi sinh viên tham gia sự kiện.',
            'buttonText' => 'QUAY VỀ',
            'buttonUrl' => url_for($controller, $listAction),
        ]);
    }

    protected function crudDetailsAction(string $controller, string $listAction, array $cfg, array $keys, callable $find, bool $canWrite, ?callable $scope = null, string $missingMessage = 'Không tìm thấy dữ liệu.'): void
    {
        $row = $find($keys);
        if (!$row) {
            $this->notFound($missingMessage);
            return;
        }
        if ($scope) {
            $scope($row);
        }
        $this->renderCrudDetails($controller, $listAction, $cfg, $row, $keys, $canWrite);
    }

    protected function crudCreateAction(string $controller, string $listAction, array $cfg, callable $create, string $title, ?array $redirect = null, ?callable $beforeWrite = null, bool $canWrite = true, array $relations = []): void
    {
        if ($this->isPost()) {
            $row = $this->collectResourceData($cfg);
            try {
                Validator::validateResource($cfg, $row);
                if ($beforeWrite) {
                    $beforeWrite($row);
                }
                $create($row);
                redirect_to($redirect['controller'] ?? $controller, $redirect['action'] ?? $listAction, $redirect['params'] ?? []);
            } catch (Throwable $e) {
                $this->renderCrudForm($controller, $listAction, $cfg, $row, 'Create', $title, $e->getMessage(), [], $canWrite, $relations);
            }
            return;
        }
        $this->renderCrudForm($controller, $listAction, $cfg, [], 'Create', $title, '', [], $canWrite, $relations);
    }

    protected function crudEditAction(string $controller, string $listAction, array $cfg, array $keys, callable $find, callable $update, string $title, ?callable $scope = null, ?callable $beforeWrite = null, bool $canWrite = true, array $relations = []): void
    {
        $row = $find($keys);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($scope) {
            $scope($row);
        }
        if ($this->isPost()) {
            $data = $this->collectResourceData($cfg, $row);
            try {
                Validator::validateResource($cfg, array_merge($data, $keys));
                if ($beforeWrite) {
                    $beforeWrite($data);
                }
                $update($keys, $data);
                redirect_to($controller, $listAction);
            } catch (Throwable $e) {
                $this->renderCrudForm($controller, $listAction, $cfg, array_merge($row, $data), 'Edit', $title, $e->getMessage(), $keys, $canWrite, $relations);
            }
            return;
        }
        $this->renderCrudForm($controller, $listAction, $cfg, $row, 'Edit', $title, '', $keys, $canWrite, $relations);
    }

    protected function crudDeleteAction(string $controller, string $listAction, array $cfg, array $keys, callable $find, callable $delete, bool $canWrite = true, ?callable $scope = null): void
    {
        $row = $find($keys);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($scope) {
            $scope($row);
        }
        if ($this->isPost()) {
            try {
                $delete($keys);
                redirect_to($controller, $listAction);
            } catch (Throwable $e) {
                $this->renderCrudDelete($controller, $listAction, $cfg, $row, $keys, $canWrite, 'Không thể xóa vì dữ liệu đang được sử dụng ở bảng khác. ' . $e->getMessage());
            }
            return;
        }
        $this->renderCrudDelete($controller, $listAction, $cfg, $row, $keys, $canWrite);
    }

    private function handleUpload(string $field, string $current = ''): string
    {
        $inputName = $field . '_upload';
        if (isset($_FILES[$inputName]) && is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
            Validator::validateImageUpload($_FILES[$inputName]);
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES[$inputName]['name']));
            $target = PUBLIC_PATH . '/Image/' . $safe;
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }
            move_uploaded_file($_FILES[$inputName]['tmp_name'], $target);
            return $safe;
        }
        return $_POST[$field] ?? $current;
    }
}
