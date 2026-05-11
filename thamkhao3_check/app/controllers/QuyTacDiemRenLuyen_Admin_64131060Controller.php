<?php
class QuyTacDiemRenLuyen_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'QuyTacDiemRenLuyen';
    protected string $controllerName = 'QuyTacDiemRenLuyen_Admin_64131060';
    protected string $listAction = 'QuyTacDiemRenLuyen_Admin_64131060';
    protected string $pageTitle = 'Quy tắc điểm rèn luyện';

    public function QuyTacDiemRenLuyen_Admin_64131060(): void { $this->index(); }

    protected function afterSuccessfulWrite(string $action, array $cfg, array $data = [], array $keys = [], array $row = []): void
    {
        $this->repo()->syncTrainingPointsFromRules();
    }
}
