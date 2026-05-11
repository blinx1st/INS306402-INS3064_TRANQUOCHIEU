<?php
class ChungNhan_Member_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'ChungNhan';
    protected string $controllerName = 'ChungNhan_Member_64131060';
    protected string $listAction = 'ChungNhan_Member_64131060';
    protected string $pageTitle = 'Chứng nhận của tôi';
    protected bool $ownOnly = true;

    public function ChungNhan_Member_64131060(): void { $this->index(); }

    public function In(...$params): void
    {
        $this->requireRoles(['TV']);
        $ma = trim($_GET['MaChungNhan'] ?? ($params[0] ?? ''));
        $cert = $this->repo()->find('ChungNhan', ['MaChungNhan' => $ma]);
        if (!$cert) {
            $this->notFound('Không tìm thấy chứng nhận.');
            return;
        }
        if ((string)$cert['MaThanhVien'] !== (string)current_member_id()) {
            $this->denyUnauthorized();
        }
        $this->render('chungnhan/print', ['title' => 'In chứng nhận', 'cert' => $cert]);
    }
}
