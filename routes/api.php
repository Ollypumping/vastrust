<?php

use App\Controllers\AuthController;
use App\Controllers\RegController;
use App\Controllers\AccountController;
use App\Controllers\TransactionController;
use App\Controllers\BeneficiaryController;
use App\Middlewares\AuthMiddleware;
use App\Helpers\ResponseHelper;
use App\Helpers\BankHelper;

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

    case 'POST /api/register-verify':
        $regController->verifyCode();
        return;
        
        

    case 'POST /api/resend-code':
        $regController->resendCode();
        return;

    case 'POST /api/update-reset-password':
        $regController->updatePasswordAfterReset();
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

if (preg_match('#^/api/setup-pin/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $authController->setupTransactionPin($matches[1]);
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

if (preg_match('#^/api/deposit/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $transactionController->deposit($matches[1]);
    return;
}

if (preg_match('#^/api/withdraw/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $transactionController->withdraw($matches[1]);
    return;
}

if (preg_match('#^/api/transfer/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $transactionController->transfer($matches[1]);
    return;
}

if (preg_match('#^/api/transactions/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $page = $_GET['page'] ?? 1;
    $transactionController->getHistory($matches[1], $page);
    return;
}

if (preg_match('#^/api/beneficiary/(\d+)/(\d+)$#', $requestUri, $matches) && $requestMethod === 'DELETE') {
    $beneficiaryController->delete($matches[2], $matches[1]); // beneficiaryId, userId
    return;
}

if (preg_match('#^/api/balance/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $accountController->getBalance($matches[1]);
    return;
}

if (preg_match('#^/api/change-pin/(\d+)$#', $requestUri, $matches) && $requestMethod === 'PUT') {
    $authController->changePin($matches[1]);
    return;
}

if (preg_match('#^/api/reset-pin/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $authController->resetPin();
    return;
}

if (preg_match('#^/api/update-reset-pin/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $authController->updatePinAfterReset();
    return;
}


// BANKS LIST
if (preg_match('#^/api/banks$#', $requestUri) && $requestMethod === 'GET') {
    $banks = BankHelper::getBanks();
    ResponseHelper::success($banks);
    return;
}


// QUERY PARAM ROUTE (optional)
// if ($routeKey === 'GET /api/balance') {
//     $accountNumber = $_GET['account_number'] ?? '';
//     $accountController->getBalance($accountNumber);
//     return;
// }

// DEFAULT
ResponseHelper::error([], 'Route not found', 404);
