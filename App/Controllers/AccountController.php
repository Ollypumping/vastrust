<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Services\AccountService;
use App\Validators\AccountValidator;
use App\Helpers\ResponseHelper;

class AccountController
{
    private $service;
    private $validator;

    public function __construct()
    {
        $this->service = new AccountService();
        $this->validator = new AccountValidator();
        AuthMiddleware::check();
    }

    public function create($userId)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $errors = $this->validator->validateCreate($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->service->create($userId, $data['account_type'] ?? 'savings');

        return $result['success']
            ? ResponseHelper::success($result['data'], $result['message'], 201)
            : ResponseHelper::error([], $result['message']);
    }

    public function getBalance($accountNumber)
    {
        $balance = $this->service->getBalance($accountNumber);

        return $balance !== false
            ? ResponseHelper::success(['balance' => $balance], 'Balance fetched successfully')
            : ResponseHelper::error([], 'Account not found', 404);
    }

    public function getAllBalances($userId)
    {
        $accounts = $this->service->getAllUserAccounts($userId);

        return ResponseHelper::success(['accounts' => $accounts], 'All account balances retrieved');
    }
}