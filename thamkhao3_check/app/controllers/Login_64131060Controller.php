<?php
class Login_64131060Controller extends Controller
{
    public function Login_64131060(): void
    {
        if ($this->isPost()) {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['matKhau'] ?? '');
            $member = $this->repo()->login($email, $password);
            if (!$member) {
                $this->render('auth/login', ['title' => 'Đăng nhập', 'error' => 'Email hoặc mật khẩu không đúng.']);
                return;
            }
            $_SESSION['Email'] = $member['Email'];
            $_SESSION['MaVaiTro'] = $member['MaVaiTro'];
            $_SESSION['MaThanhVien'] = $member['MaThanhVien'];
            if ($member['MaVaiTro'] === 'TVCN') {
                redirect_to('TrangChu_64131060', 'AdminPage_64131060');
            }
            if ($member['MaVaiTro'] === 'TVTG') {
                redirect_to('TrangChu_64131060', 'AssistantPage_64131060');
            }
            if ($member['MaVaiTro'] === 'TV') {
                redirect_to('TrangChu_64131060', 'MemberPage_64131060');
            }
            $this->render('auth/login', ['title' => 'Đăng nhập', 'error' => 'Vai trò không hợp lệ.']);
            return;
        }
        $this->render('auth/login', ['title' => 'Đăng nhập']);
    }

    public function Logout_64131060(): void
    {
        session_unset();
        session_destroy();
        session_start();
        redirect_to('Login_64131060', 'Login_64131060');
    }
}
