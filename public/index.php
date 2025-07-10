<?php
// CORS headers for all requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

// --- 1. Normalize Request URI ---
$method = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?'); // remove query string
$uri = rtrim($uri, '/');

// --- 2. Set base path (adjust this if your folder is different) ---
$basePath = '/vastrust/public';

// Remove the basePath from the URI so it works in subfolders
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Prepend /api to unify routing logic (optional)
if (strpos($uri, '/api') !== 0) {
    $uri = '/api' . $uri;
}
// Store back into $_SERVER for router to read
$_SERVER['REQUEST_URI'] = $uri;
$_SERVER['REQUEST_METHOD'] = $method;

// --- 3. Manual Includes ---
require_once '../config/database.php';
require_once __DIR__ . '/../autoload.php';

require_once '../app/helpers/ResponseHelper.php';

require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/AccountController.php';
require_once '../app/controllers/TransactionController.php';
require_once '../app/services/AuthService.php';



require_once '../routes/api.php';

//installed for jwt
//composer require firebase/php-jwt