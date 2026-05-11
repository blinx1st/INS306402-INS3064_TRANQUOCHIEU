<?php
class DiemDanh_Member_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'DiemDanh';
    protected string $controllerName = 'DiemDanh_Member_64131060';
    protected string $listAction = 'DiemDanh_Member_64131060';
    protected string $pageTitle = 'Điểm danh (Thành viên)';
    protected array $afterCreate = ['controller' => 'DiemDanh_Member_64131060', 'action' => 'Alert_Member_64131060'];

    public function DiemDanh_Member_64131060(): void { $this->index(); }
    public function Alert_Member_64131060(): void { $this->renderAlert('Điểm danh thành công', 'Thông tin điểm danh của bạn đã được ghi nhận.', 'QUAY VỀ', 'TrangChu_64131060', 'MemberPage_64131060'); }
}
