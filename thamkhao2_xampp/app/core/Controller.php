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
}
