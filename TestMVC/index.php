<?php

session_start();

require __DIR__ . '/config/app.php';
require APP_ROOT . '/core/helpers.php';

spl_autoload_register(function ($class) {
    $paths = [
        APP_ROOT . '/core/' . $class . '.php',
        APP_ROOT . '/app/Controllers/' . $class . '.php',
        APP_ROOT . '/app/Models/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$router = new Router();

$router->get('', [StudentsController::class, 'index']);
$router->get('students', [StudentsController::class, 'index']);
$router->get('students/create', [StudentsController::class, 'create']);
$router->post('students/store', [StudentsController::class, 'store']);
$router->get('students/edit/{id}', [StudentsController::class, 'edit']);
$router->post('students/update/{id}', [StudentsController::class, 'update']);
$router->post('students/delete/{id}', [StudentsController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_GET['url'] ?? '');

