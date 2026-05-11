<?php
class DiemRenLuyen_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'DiemRenLuyen';
    protected string $controllerName = 'DiemRenLuyen_Admin_64131060';
    protected string $listAction = 'DiemRenLuyen_Admin_64131060';
    protected string $pageTitle = 'Điểm rèn luyện';

    public function DiemRenLuyen_Admin_64131060(): void { $this->index(); }

    public function ExportCsv(): void
    {
        $this->requireRoles(['TVCN']);
        $hocKy = trim($_GET['HocKy'] ?? '');
        $namHoc = trim($_GET['NamHoc'] ?? '');
        $maCLB = trim($_GET['MaCLB'] ?? '') ?: null;
        Validator::validateResource([
            'table' => 'ExportDiem',
            'fields' => [
                'HocKy' => ['label' => 'Học kỳ', 'type' => 'select_static', 'required' => true],
                'NamHoc' => ['label' => 'Năm học', 'type' => 'text', 'required' => true, 'pattern' => '/^\d{4}-\d{4}$/'],
            ],
        ], ['HocKy' => $hocKy, 'NamHoc' => $namHoc]);

        $rows = $this->repo()->termPointTotals($hocKy, $namHoc, $maCLB);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="diem-ren-luyen-' . $hocKy . '-' . $namHoc . '.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Mã thành viên', 'Họ tên', 'Email', 'Học kỳ', 'Năm học', 'Tổng điểm', 'Cập nhật lúc']);
        foreach ($rows as $row) {
            fputcsv($out, [$row['MaThanhVien'], $row['HoTen'], $row['Email'], $row['HocKy'], $row['NamHoc'], $row['TongDiem'], $row['CapNhatLuc']]);
        }
        fclose($out);
        exit;
    }
}
