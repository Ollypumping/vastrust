<?php

require_once _DIR_ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

session_start();

// Load environment variables
$dotenv = Dotenv::createImmutable(_DIR_ . '/../');
$dotenv->load();

// Get current request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?'); // Strip query params

$routeKey = "$method $uri";

// Load routes
$routes = require _DIR_ . '/../routes/api.php';

// Route handling
if (array_key_exists($routeKey, $routes)) {
    $handler = $routes[$routeKey];

    if (is_array($handler)) {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        return $controller->$method();
    }

    if (is_callable($handler)) {
        return $handler();
    }
}

// If route not found
http_response_code(404);
echo json_encode([
    'status' => 'error',
    'message' => 'Route not found'
]);