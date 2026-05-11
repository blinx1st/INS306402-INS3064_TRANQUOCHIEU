<?php
class SuKien_Assitant_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'SuKien';
    protected string $controllerName = 'SuKien_Assitant_64131060';
    protected string $listAction = 'TimKiemSuKien_Assitant_64131060';
    protected string $pageTitle = 'Trang sự kiện (Trợ giảng)';

    public function SuKien_Assitant_64131060(): void { $this->index(); }
    public function TimKiemSuKien_Assitant_64131060(): void { $this->searchEvents('TimKiemSuKien_Assitant_64131060'); }
}
