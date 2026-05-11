<?php
class DiemDanh_Assitant_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'DiemDanh';
    protected string $controllerName = 'DiemDanh_Assitant_64131060';
    protected string $listAction = 'DiemDanh_Assitant_64131060';
    protected string $pageTitle = 'Điểm danh (Trợ giảng)';
    protected array $afterCreate = ['controller' => 'DiemDanh_Assitant_64131060', 'action' => 'Create'];
    public function DiemDanh_Assitant_64131060(): void { $this->index(); }
}
