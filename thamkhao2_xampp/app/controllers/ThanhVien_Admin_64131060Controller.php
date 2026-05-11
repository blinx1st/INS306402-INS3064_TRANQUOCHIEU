<?php
class ThanhVien_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'ThanhVien';
    protected string $controllerName = 'ThanhVien_Admin_64131060';
    protected string $listAction = 'TimKiemTV_Admin_64131060';
    protected string $pageTitle = 'Thành viên của câu lạc bộ (Chủ nhiệm)';

    public function ThanhVien_Admin_64131060(): void { $this->index(); }
    public function TimKiemTV_Admin_64131060(): void { $this->searchMembers('TimKiemTV_Admin_64131060'); }
    public function Admin_Page_64131060(): void { $this->renderProfile('Edit_Admin_64131060'); }
    public function Edit_Admin_64131060(...$params): void { $this->Edit(...$params); }
}
