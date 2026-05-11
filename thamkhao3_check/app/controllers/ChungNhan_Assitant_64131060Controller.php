<?php
class ChungNhan_Assitant_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'ChungNhan';
    protected string $controllerName = 'ChungNhan_Assitant_64131060';
    protected string $listAction = 'ChungNhan_Assitant_64131060';
    protected string $pageTitle = 'Chứng nhận (Trợ giảng)';

    public function ChungNhan_Assitant_64131060(): void { $this->index(); }

    public function In(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $ma = trim($_GET['MaChungNhan'] ?? ($params[0] ?? ''));
        $cert = $this->repo()->find('ChungNhan', ['MaChungNhan' => $ma]);
        if (!$cert) {
            $this->notFound('Không tìm thấy chứng nhận.');
            return;
        }
        if (!$this->repo()->canManageEvent((string)$cert['MaSuKien'], (string)current_member_id())) {
            $this->denyUnauthorized();
        }
        $this->render('chungnhan/print', ['title' => 'In chứng nhận', 'cert' => $cert]);
    }
}
