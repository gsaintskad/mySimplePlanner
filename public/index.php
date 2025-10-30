<?php
session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Controllers\AuthController;
use App\Controllers\PageController;

// Routing na podstawie adresu URL
$request_uri = strtok($_SERVER["REQUEST_URI"], '?'); // Usuń query string
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$pageController = new PageController();

switch ($request_uri) {
    case '/':
        if ($method === 'POST') {
            $authController->handleLogin();
        } else {
            $authController->showLoginForm();
        }
        break;

    case '/register':
        if ($method === 'POST') {
            $authController->handleRegistration();
        } else {
            $authController->showRegistrationForm();
        }
        break;

    case '/main':
        $pageController->main();
        break;

    case '/logout':
        $authController->logout();
        break;

    default:
        // Obsługa 404
        http_response_code(404);
        echo "404 - Strona nie znaleziona";
        break;
}