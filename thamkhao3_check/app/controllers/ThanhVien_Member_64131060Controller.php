<?php
class ThanhVien_Member_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'ThanhVien';
    protected string $controllerName = 'ThanhVien_Member_64131060';
    protected string $listAction = 'Index';
    protected string $pageTitle = 'Thành viên';
    protected array $afterCreate = ['controller' => 'ThanhVien_Member_64131060', 'action' => 'SigninAlert_64131060'];

    public function Index(): void { $this->index(); }
    public function Member_Page_64131060(): void { $this->renderProfile('Edit_Member_64131060'); }
    public function Edit_Member_64131060(...$params): void { $this->Edit(...$params); }
    public function SigninAlert_64131060(): void { $this->renderAlert('Đăng ký thành công', 'Tài khoản thành viên đã được tạo.', 'ĐĂNG NHẬP', 'Login_64131060', 'Login_64131060'); }
}
