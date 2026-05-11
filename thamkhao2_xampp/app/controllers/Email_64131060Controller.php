<?php
class Email_64131060Controller extends Controller
{
    public function SendMail_Asstant_64131060(): void { $this->send('Gửi email (Trợ giảng)', 'MailAlert1_64131060'); }
    public function SendMail_Admin_64131060(): void { $this->send('Gửi email (Chủ nhiệm)', 'MailAlert2_64131060'); }
    public function SendMail_Member_64131060(): void { $this->send('Gửi email (Thành viên)', 'MailAlert3_64131060'); }
    public function MailAlert1_64131060(): void { $this->alert(); }
    public function MailAlert2_64131060(): void { $this->alert(); }
    public function MailAlert3_64131060(): void { $this->alert(); }

    private function send(string $title, string $successAction): void
    {
        if ($this->isPost()) {
            try {
                (new Mailer())->send($_POST['From'] ?? '', $_POST['Password'] ?? '', $_POST['To'] ?? '', $_POST['Subject'] ?? '', $_POST['Body'] ?? '');
                redirect_to('Email_64131060', $successAction);
            } catch (Throwable $e) {
                $this->render('email/send', ['title' => $title, 'error' => $e->getMessage()]);
            }
            return;
        }
        $this->render('email/send', ['title' => $title]);
    }

    private function alert(): void
    {
        $home = current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : 'MemberPage_64131060');
        $this->render('generic/message', ['title' => 'Gửi email thành công', 'message' => 'Email đã được gửi.', 'buttonText' => 'QUAY VỀ', 'buttonUrl' => url_for('TrangChu_64131060', $home)]);
    }
}
