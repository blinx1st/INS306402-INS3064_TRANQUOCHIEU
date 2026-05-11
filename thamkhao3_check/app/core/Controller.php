<?php
class Controller
{
    protected ?Repository $repo = null;

    public function __construct()
    {
    }

    protected function repo(): Repository
    {
        if ($this->repo === null) {
            $this->repo = new Repository();
        }
        return $this->repo;
    }

    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . h($view);
            return;
        }
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (is_file($layoutFile)) {
            require $layoutFile;
            return;
        }
        echo $content;
    }

    protected function notFound(string $message = 'Không tìm thấy dữ liệu.'): void
    {
        http_response_code(404);
        $this->render('generic/message', ['title' => 'Không tìm thấy', 'message' => $message]);
    }

    protected function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function requireLogin(): void
    {
        if (!current_role()) {
            if ($this->wantsJson()) {
                $this->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
            }
            redirect_to('Login_64131060', 'Login_64131060');
        }
    }

    protected function requireRoles(array $roles): void
    {
        $this->requireLogin();
        if (!in_array((string)current_role(), $roles, true)) {
            $this->denyUnauthorized();
        }
    }

    protected function denyUnauthorized(): void
    {
        if ($this->wantsJson()) {
            $this->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }
        http_response_code(403);
        $home = current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : 'MemberPage_64131060');
        $this->render('generic/message', [
            'title' => 'Không có quyền',
            'message' => 'Bạn không có quyền truy cập chức năng này.',
            'buttonText' => 'QUAY VỀ',
            'buttonUrl' => url_for('TrangChu_64131060', $home),
        ]);
        exit;
    }

    protected function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
