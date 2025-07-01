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

}

// ⛔ All routes below require authentication
new AuthMiddleware();

// PROTECTED CONTROLLERS
$authController = new AuthController();
$accountController = new AccountController();
$transactionController = new TransactionController();
$beneficiaryController = new BeneficiaryController(); 

// PROTECTED ROUTES
switch ($routeKey) {
    case 'POST /api/login':
        $authController->login();
        return;
    case 'GET /api/profile':
        $authController->profile($_SESSION['user_id'] ?? null);
        break;

    case 'PUT /api/change-password':
        $authController->changePassword($_SESSION['user_id'] ?? null);
        break;

    case 'POST /api/create-account':
        $accountController->create($_SESSION['user_id'] ?? null);
        break;

    case 'GET /api/balance':
        $accountNumber = $_GET['account_number'] ?? '';
        $accountController->getBalance($accountNumber);
        break;

    case 'GET /api/balances':
        $accountController->getAllBalances($_SESSION['user_id'] ?? null);
        break;

    case 'POST /api/deposit':
        $transactionController->deposit();
        break;

    case 'POST /api/withdraw':
        $transactionController->withdraw();
        break;

    case 'POST /api/transfer':
        $transactionController->transfer();
        break;

    case 'GET /api/beneficiaries':
        $beneficiaryController->list($_SESSION['user_id'] ?? null);
        break;

    case 'DELETE /api/beneficiary':
        $id = $_GET['id'] ?? null;
        $beneficiaryController->delete($id);
        break;


    case 'GET /api/transactions':
        $account = $_GET['account'] ?? '';
        $page = $_GET['page'] ?? 1;
        $transactionController->list($account, $page);
        break;

    default:
        ResponseHelper::error([], 'Route not found', 404);
        break;
}