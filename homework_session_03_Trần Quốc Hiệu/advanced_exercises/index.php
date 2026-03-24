<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        include 'views/home.php';
        break;

    case 'about':
        include 'views/about.php';
        break;

    case 'contact':
        include 'views/contact.php';
        break;

    default:
        http_response_code(404);
        echo "404 - Page not found";
}
