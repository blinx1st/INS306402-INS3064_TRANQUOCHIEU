<?php

class Router
{
    private $routes = [];

    public function get($pattern, $handler)
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post($pattern, $handler)
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add($method, $pattern, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => trim($pattern, '/'),
            'handler' => $handler,
        ];
    }

    public function dispatch($method, $path)
    {
        $method = strtoupper($method);
        $path = trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['pattern'], $path);
            if ($params === false) {
                continue;
            }

            $controller = new $route['handler'][0]();
            $action = $route['handler'][1];

            return call_user_func_array([$controller, $action], $params);
        }

        return $this->render404();
    }

    private function match($pattern, $path)
    {
        if ($pattern === $path) {
            return [];
        }

        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return false;
        }

        array_shift($matches);

        return $matches;
    }

    private function render404()
    {
        http_response_code(404);

        $title = '404';
        $message = 'Duong dan ban truy cap khong ton tai.';
        ob_start();
        require APP_ROOT . '/app/Views/errors/404.php';
        $content = ob_get_clean();

        require APP_ROOT . '/app/Views/layouts/app.php';
    }
}

