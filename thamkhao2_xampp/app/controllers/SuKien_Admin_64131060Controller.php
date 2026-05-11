<?php
class SuKien_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'SuKien';
    protected string $controllerName = 'SuKien_Admin_64131060';
    protected string $listAction = 'TimKiemSuKien_Admin_64131060';
    protected string $pageTitle = 'Trang sự kiện (Chủ nhiệm)';

    public function SuKien_Admin_64131060(): void { $this->index(); }
    public function TimKiemSuKien_Admin_64131060(): void { $this->searchEvents('TimKiemSuKien_Admin_64131060'); }
}
