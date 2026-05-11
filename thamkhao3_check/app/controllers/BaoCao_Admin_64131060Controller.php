<?php
class BaoCao_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'BaoCao';
    protected string $controllerName = 'BaoCao_Admin_64131060';
    protected string $listAction = 'BaoCao_Admin_64131060';
    protected string $pageTitle = 'Báo cáo';

    public function BaoCao_Admin_64131060(): void { $this->index(); }

    public function ThongKe(): void
    {
        $this->requireRoles(['TVCN']);
        $hocKy = trim($_GET['HocKy'] ?? '') ?: null;
        $namHoc = trim($_GET['NamHoc'] ?? '') ?: null;
        $maCLB = trim($_GET['MaCLB'] ?? '') ?: null;
        $stats = $this->repo()->dashboardStats($hocKy, $namHoc, $maCLB);
        $this->render('baocao/dashboard', [
            'title' => 'Thống kê tổng hợp',
            'stats' => $stats,
            'filters' => ['HocKy' => $hocKy, 'NamHoc' => $namHoc, 'MaCLB' => $maCLB],
            'clbs' => $this->repo()->options(['table' => 'CLB', 'value' => 'MaCLB', 'label' => 'TenCLB']),
        ]);
    }
}
