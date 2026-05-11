<?php
class SuKien_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'SuKien';
    protected string $controllerName = 'SuKien_64131060';
    protected string $listAction = 'TimKiemSuKien_64131060';
    protected string $pageTitle = 'Trang sự kiện';

    public function SuKien_64131060(): void { $this->index(); }
    public function TimKiemSuKien_64131060(): void { $this->searchEvents('TimKiemSuKien_64131060'); }
}
