<?php
class ChungNhan_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'ChungNhan';
    protected string $controllerName = 'ChungNhan_Admin_64131060';
    protected string $listAction = 'ChungNhan_Admin_64131060';
    protected string $pageTitle = 'Chứng nhận';

    public function ChungNhan_Admin_64131060(): void { $this->index(); }

    public function In(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $ma = trim($_GET['MaChungNhan'] ?? ($params[0] ?? ''));
        $cert = $this->repo()->find('ChungNhan', ['MaChungNhan' => $ma]);
        if (!$cert) {
            $this->notFound('Không tìm thấy chứng nhận.');
            return;
        }
        $this->render('chungnhan/print', ['title' => 'In chứng nhận', 'cert' => $cert]);
    }
}
