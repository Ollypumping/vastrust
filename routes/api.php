<?php

use App\Controllers\ProfileController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\AdminAuthController;
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
$authController = new AuthController();
$adminAuthController = new AdminAuthController();

switch ($routeKey) {
    case 'POST /api/register':
        $authController->register();
        return;

    case 'POST /api/forgot-password':
        $authController->forgotPassword();
        return;

    case 'POST /api/login':
        $authController->login();
        return;

    case 'POST /api/register-verify':
        $authController->verifyCode();
        return;
        
        

    case 'POST /api/resend-code':
        $authController->resendCode();
        return;

    case 'POST /api/reset-password':
        $authController->resetPassword();
        return;

    case 'POST /api/admin/login':
        $adminAuthController->login();
        return;

    case 'POST /api/admin/register':
        $adminAuthController->register();
        return;
}

// PROTECTED CONTROLLERS
$profileController = new ProfileController();
$accountController = new AccountController();
$transactionController = new TransactionController();
$beneficiaryController = new BeneficiaryController(); 



// MIDDLEWARE (apply globally to protected routes)
new AuthMiddleware();

// PROTECTED ROUTES WITH user_id IN URL

if (preg_match('#^/api/profile/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $profileController->profile($matches[1]);
    return;
}

if (preg_match('#^/api/upload-passport/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $req = (object)[ 'params' => ['userId' => $matches[1]] ];
    $profileController->uploadPassportPhoto($req, $res = null);
    return;
}


if (preg_match('#^/api/change-password/(\d+)$#', $requestUri, $matches) && $requestMethod === 'PUT') {
    $profileController->changePassword($matches[1]);
    return;
}

if (preg_match('#^/api/setup-pin/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $profileController->setupTransactionPin($matches[1]);
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
    $profileController->changePin($matches[1]);
    return;
}

if (preg_match('#^/api/forgot-pin/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $profileController->forgotPin();
    return;
}

if (preg_match('#^/api/reset-pin/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    $profileController->resetPin();
    return;
}


// BANKS LIST
if (preg_match('#^/api/banks$#', $requestUri) && $requestMethod === 'GET') {
    $banks = BankHelper::getBanks();
    ResponseHelper::success($banks);
    return;
}



// ADMIN ROUTES

if (preg_match('#^/api/admin/(\d+)/users$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $adminController = new AdminController($matches[1]);
    $adminController->getAllUsers();
    return;
}

if (preg_match('#^/api/admin/(\d+)/users/(\d+)/accounts$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $adminController = new AdminController($matches[1]);
    $adminController->getUserAccounts($matches[1], $matches[2]);
    return;
}

if (preg_match('#^/api/admin/(\d+)/users/(\d+)/deactivate$#', $requestUri, $matches) && $requestMethod === 'PATCH') {
    $adminController = new AdminController($matches[1]);
    $adminController->deactivateUser($matches[2], $matches[1]);
    return;
}

if (preg_match('#^/api/admin/(\d+)/users/(\d+)/activate$#', $requestUri, $matches) && $requestMethod === 'PATCH') {
    $adminController = new AdminController($matches[1]);
    $adminController->activateUser($matches[2], $matches[1]);
    return;
}

if (preg_match('#^/api/admin/(\d+)/users/(\d+)/update$#', $requestUri, $matches) && $requestMethod === 'PUT') {
    $adminController = new AdminController($matches[1]);
    $data = json_decode(file_get_contents('php://input'), true);
    $adminController->updateUser($matches[2], $matches[1], $data);
    return;
}

if (preg_match('#^/api/admin/(\d+)/transactions$#', $requestUri, $matches) && $requestMethod === 'GET') {
    $adminController = new AdminController($matches[1]);
    $adminController->getAllTransactions($matches[1]);
    return;
}

if (preg_match('#^/api/admin/(\d+)/users/(\d+)/change-password$#', $requestUri, $matches) && $requestMethod === 'PATCH') {
    $adminController = new AdminController($matches[1]);
    $data = json_decode(file_get_contents('php://input'), true);
    $adminController->changeUserPassword($matches[2], $matches[1], $data['new_password']);
    return;
}

if (preg_match('#^/api/admin/(\d+)/users/(\d+)/change-pin$#', $requestUri, $matches) && $requestMethod === 'PATCH') {
    $adminController = new AdminController($matches[1]);
    $data = json_decode(file_get_contents('php://input'), true);
    $adminController->changeUserPin($matches[2], $matches[1], $data['new_pin']);
    return;
}


// DEFAULT
ResponseHelper::error([], 'Route not found', 404);
