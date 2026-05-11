<?php
class SuKien_Admin_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'SuKien';
    protected string $controllerName = 'SuKien_Admin_64131060';
    protected string $listAction = 'TimKiemSuKien_Admin_64131060';
    protected string $pageTitle = 'Trang sự kiện (Chủ nhiệm)';

    public function SuKien_Admin_64131060(): void { $this->index(); }
    public function TimKiemSuKien_Admin_64131060(): void { $this->searchEvents('TimKiemSuKien_Admin_64131060'); }

    public function QRCode(): void
    {
        $this->requireRoles(['TVCN']);
        $maSuKien = trim($_GET['MaSuKien'] ?? '');
        $event = $this->repo()->find('SuKien', ['MaSuKien' => $maSuKien]);
        if (!$event) {
            $this->notFound('Không tìm thấy sự kiện.');
            return;
        }
        $token = $this->repo()->ensureEventToken($maSuKien);
        $scanUrl = url_for('CheckInSuKien_64131060', 'Scan', ['MaSuKien' => $maSuKien, 'Token' => $token]);
        $this->render('sukien/qr', ['title' => 'QR check-in sự kiện', 'event' => $event, 'scanUrl' => $scanUrl, 'backUrl' => url_for($this->controllerName, $this->listAction)]);
    }
}
