<?php
class DiemDanh_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'DiemDanh';
    protected string $controllerName = 'DiemDanh_Admin_64131060';
    protected string $listAction = 'DiemDanh_Admin_64131060';
    protected string $pageTitle = 'Điểm danh (Chủ nhiệm)';
    protected array $afterCreate = ['controller' => 'DiemDanh_Admin_64131060', 'action' => 'Create'];
    public function DiemDanh_Admin_64131060(): void { $this->index(); }
}
