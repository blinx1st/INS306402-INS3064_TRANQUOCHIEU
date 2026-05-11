<?php

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        ob_start();
        require APP_ROOT . '/app/Views/' . $view . '.php';
        $content = ob_get_clean();

        require APP_ROOT . '/app/Views/layouts/app.php';
    }

    protected function notFound($message = 'Khong tim thay du lieu.')
    {
        http_response_code(404);

        $title = '404';
        ob_start();
        require APP_ROOT . '/app/Views/errors/404.php';
        $content = ob_get_clean();

        require APP_ROOT . '/app/Views/layouts/app.php';
    }
}

