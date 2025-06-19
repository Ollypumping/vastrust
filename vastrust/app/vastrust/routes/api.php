<?php

use App\Controllers\AuthController;
use App\Controllers\AccountController;
use App\Controllers\TransactionController;
use App\Helpers\ResponseHelper;

// Instantiate controllers once
$authController = new AuthController();
$accountController = new AccountController();
$transactionController = new TransactionController();

$requestUri = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$requestMethod = $_SERVER['REQUEST_METHOD'];
$routeKey = "$requestMethod $requestUri";

switch ($routeKey) {
    // Authentication
    case 'POST /api/register':
        $authController->register();
        break;

    case 'POST /api/login':
        $authController->login();
        break;

    case 'GET /api/profile':
        $authController->profile($_SESSION['user_id'] ?? null);
        break;

    case 'PUT /api/change-password':
        $authController->changePassword($_SESSION['user_id'] ?? null);
        break;

    case 'POST /api/reset-password':
        $authController->resetPassword();
        break;

    // Account
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

    // Transactions
    case 'POST /api/deposit':
        $transactionController->deposit();
        break;
    case 'POST /api/withdraw':
        $transactionController->withdraw();
        break;

    case 'POST /api/transfer':
        $transactionController->transfer();
        break;

    case 'GET /api/transactions':
        $account = $_GET['account'] ?? '';
        $page = $_GET['page'] ?? 1;
        $transactionController->getTransactionHistory($account, $page);
        break;

    default:
        ResponseHelper::error([], 'Route not found', 404);
        break;
}