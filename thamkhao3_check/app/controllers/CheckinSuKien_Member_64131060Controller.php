<?php
class CheckinSuKien_Member_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'CheckinSuKien';
    protected string $controllerName = 'CheckinSuKien_Member_64131060';
    protected string $listAction = 'CheckinSuKien_Member_64131060';
    protected string $pageTitle = 'Lịch sử check-in của tôi';
    protected bool $ownOnly = true;

    public function CheckinSuKien_Member_64131060(): void { $this->index(); }
}
