<?php
class ThanhVien_Member_64131060Controller extends ResourceController
{
    protected string $resourceKey = 'ThanhVien';
    protected string $controllerName = 'ThanhVien_Member_64131060';
    protected string $listAction = 'Index';
    protected string $pageTitle = 'Thành viên';
    protected array $afterCreate = ['controller' => 'ThanhVien_Member_64131060', 'action' => 'SigninAlert_64131060'];

    public function Index(): void { $this->index(); }

    public function Create(): void
    {
        $cfg = $this->registrationConfig();
        if ($this->isPost()) {
            $row = [
                'MaThanhVien' => trim($_POST['MaThanhVien'] ?? ''),
                'HoTen' => trim($_POST['HoTen'] ?? ''),
                'Email' => trim($_POST['Email'] ?? ''),
                'MatKhau' => trim($_POST['MatKhau'] ?? ''),
            ];
            try {
                Validator::validateResource($cfg, $row);
                $this->repo()->insert('ThanhVien', $row + ['MaVaiTro' => 'TV']);
                redirect_to('ThanhVien_Member_64131060', 'SigninAlert_64131060');
            } catch (PDOException $e) {
                $this->renderRegistrationForm($cfg, $row, 'Mã số hoặc email đã tồn tại, vui lòng kiểm tra lại.');
            } catch (Throwable $e) {
                $this->renderRegistrationForm($cfg, $row, $e->getMessage());
            }
            return;
        }
        $this->renderRegistrationForm($cfg);
    }

    public function Member_Page_64131060(): void { $this->renderProfile('Edit_Member_64131060'); }
    public function Edit_Member_64131060(...$params): void { $this->Edit(...$params); }
    public function SigninAlert_64131060(): void { $this->renderAlert('Đăng ký thành công', 'Tài khoản thành viên đã được tạo.', 'ĐĂNG NHẬP', 'Login_64131060', 'Login_64131060'); }

    private function registrationConfig(): array
    {
        return [
            'table' => 'ThanhVien',
            'pk' => ['MaThanhVien'],
            'auto' => [],
            'title' => 'Đăng ký tài khoản',
            'fields' => [
                'MaThanhVien' => ['label' => 'Mã số', 'type' => 'text', 'required' => true, 'max_length' => 50],
                'HoTen' => ['label' => 'Họ tên', 'type' => 'text', 'required' => true, 'max_length' => 100],
                'Email' => ['label' => 'Email', 'type' => 'email', 'required' => true, 'max_length' => 100],
                'MatKhau' => ['label' => 'Mật khẩu', 'type' => 'password', 'required' => true, 'max_length' => 255],
            ],
            'list' => [],
        ];
    }

    private function renderRegistrationForm(array $cfg, array $row = [], string $error = ''): void
    {
        $this->render('generic/form', [
            'cfg' => $cfg,
            'row' => $row,
            'action' => 'Create',
            'title' => 'Đăng ký tài khoản',
            'error' => $error,
            'keys' => [],
            'relations' => [],
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'canWrite' => true,
            'backUrl' => url_for('Login_64131060', 'Login_64131060'),
            'backText' => 'QUAY VỀ ĐĂNG NHẬP',
        ]);
    }
}
