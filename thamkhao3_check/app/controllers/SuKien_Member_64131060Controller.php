<?php
class SuKien_Member_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'SuKien';
    protected string $controllerName = 'SuKien_Member_64131060';
    protected string $listAction = 'TimKiemSuKien_Member_64131060';
    protected string $pageTitle = 'Trang sự kiện (Thành viên)';

    public function SuKien_Member_64131060(): void { $this->index(); }
    public function TimKiemSuKien_Member_64131060(): void { $this->searchEvents('TimKiemSuKien_Member_64131060'); }
}
