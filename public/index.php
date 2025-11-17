<?php
// Ustaw domyślny typ odpowiedzi na JSON
header('Content-Type: application/json');

// 1. ZAŁADUJ AUTOLOADER COMPOSERA
// Musi być na samej górze, aby załadować bibliotekę JWT
require __DIR__ . '/../vendor/autoload.php';

// 2. SESJA JEST JUŻ NIEPOTRZEBNA (usunięto session_start())

// Autoloader aplikacji (bez zmian)
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
use App\Controllers\TaskController;

// Prosty router API
$request_uri = strtok($_SERVER["REQUEST_URI"], '?');
$method = $_SERVER['REQUEST_METHOD'];

// Rozbij URI na części
$parts = explode('/', trim($request_uri, '/'));
$apiPrefix = $parts[0] ?? null; // 'api'
$resource = $parts[1] ?? null;  // 'login', 'register', 'tasks'
$resourceId = $parts[2] ?? null; // np. ID zadania '123'

if ($apiPrefix !== 'api') {
    http_response_code(404);
    echo json_encode(['error' => 'API not found. Use /api endpoint']);
    exit;
}

// Routing dla Auth
if ($resource === 'register' && $method === 'POST') {
    (new AuthController())->handleRegistration();
    exit;
}

if ($resource === 'login' && $method === 'POST') {
    (new AuthController())->handleLogin();
    exit;
}

if ($resource === 'refresh' && $method === 'POST') {
    (new AuthController())->handleRefresh();
    exit;
}

if ($resource === 'logout' && $method === 'POST') {
    (new AuthController())->handleLogout();
    exit;
}

// Routing dla Tasks
if ($resource === 'tasks') {
    $controller = new TaskController();

    if ($method === 'GET') {
        if ($resourceId) {
            $controller->getTask((int)$resourceId); // GET /api/tasks/{id}
        } else {
            $controller->listTasks(); // GET /api/tasks
        }
    } 
    elseif ($method === 'POST') {
        $controller->create(); // POST /api/tasks
    } 
    elseif ($method === 'PUT' && $resourceId) {
        $controller->update((int)$resourceId); // PUT /api/tasks/{id}
    } 
    elseif ($method === 'DELETE' && $resourceId) {
        $controller->delete((int)$resourceId); // DELETE /api/tasks/{id}
    } 
    else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Method Not Allowed for this resource']);
    }
    exit;
}

// Domyślny błąd 404
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);