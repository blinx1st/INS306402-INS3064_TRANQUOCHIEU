<?php
// index.php

// Autoload classes (if using an autoloader)
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

// Get the requested page from the query parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Define a simple routing mechanism
switch ($page) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;
    case 'about':
        $controller = new AboutController();
        $controller->index();
        break;
    case 'contact':
        $controller = new ContactController();
        $controller->index();
        break;
    default:
        // Show 404 page
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
        break;
}

// Example Controllers
class HomeController {
    public function index() {
        echo "<h1>Home Page</h1>";
    }
}

class AboutController {
    public function index() {
        echo "<h1>About Page</h1>";
    }
}

class ContactController {
    public function index() {
        echo "<h1>Contact Page</h1>";
    }
}
?>