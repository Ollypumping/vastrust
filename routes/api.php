<?php

use App\Controllers\AuthController;
use App\Controllers\RegController;
use App\Controllers\AccountController;
use App\Controllers\TransactionController;
use App\Controllers\BeneficiaryController;
use App\Middlewares\AuthMiddleware;
use App\Helpers\ResponseHelper;

$requestUri = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$requestMethod = $_SERVER['REQUEST_METHOD'];
$routeKey = "$requestMethod $requestUri";

// PUBLIC ROUTES
$regController = new RegController();

switch ($routeKey) {
    case 'POST /api/register':
        $regController->register();
        return;

    case 'POST /api/reset-password':
        $regController->resetPassword();
        return;

    case 'POST /api/login':
        $regController->login();
        return;
}

// PROTECTED CONTROLLERS
$authController = new AuthController();
$accountController = new AccountController();
$transactionController = new TransactionController();
$beneficiaryController = new BeneficiaryController(); 

// MIDDLEWARE (apply globally to protected routes)
new AuthMiddleware();

// PROTECTED ROUTES WITH user_id IN URL

if (preg_match('#^/api/profile/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $authController->profile($matches[1]);
    return;
}

if (preg_match('#^/api/change-password/(\d+)$#', $requestUri, $matches) && $requestMethod === 'PUT') {
    $authController->changePassword($matches[1]);
    return;
}

if (preg_match('#^/api/create-account/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $accountController->create($matches[1]);
    return;
}

if (preg_match('#^/api/balances/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $accountController->getAllBalances($matches[1]);
    return;
}

if (preg_match('#^/api/beneficiaries/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $beneficiaryController->list($matches[1]);
    return;
}

// OTHER PROTECTED ROUTES (query parameters only)

if ($routeKey === 'GET /api/balance') {
    $accountNumber = $_GET['account_number'] ?? '';
    $accountController->getBalance($accountNumber);
    return;
}

if ($routeKey === 'POST /api/deposit') {
    $transactionController->deposit();
    return;
}

if ($routeKey === 'POST /api/withdraw') {
    $transactionController->withdraw();
    return;
}

if ($routeKey === 'POST /api/transfer') {
    $transactionController->transfer();
    return;
}

if ($routeKey === 'DELETE /api/beneficiary') {
    $id = $_GET['id'] ?? null;
    $beneficiaryController->delete($id);
    return;
}

if ($routeKey === 'GET /api/transactions') {
    $account = $_GET['account'] ?? '';
    $page = $_GET['page'] ?? 1;
    $transactionController->getHistory($account, $page);
    return;
}

// DEFAULT
ResponseHelper::error([], 'Route not found', 404);