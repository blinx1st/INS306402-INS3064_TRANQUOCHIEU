<?php
class ThanhVien_Assitant_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'ThanhVien';
    protected string $controllerName = 'ThanhVien_Assitant_64131060';
    protected string $listAction = 'TimKiemTV_Assitant_64131060';
    protected string $pageTitle = 'Thành viên của câu lạc bộ (Trợ giảng)';

    public function ThanhVien_Assitant_64131060(): void { $this->index(); }
    public function TimKiemTV_Assitant_64131060(): void { $this->searchMembers('TimKiemTV_Assitant_64131060'); }
    public function Assitant_Page_64131060(): void { $this->renderProfile('Edit_Assistant_64131060'); }
    public function Edit_Assistant_64131060(...$params): void { $this->Edit(...$params); }
}
