<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Services\TransactionService;
use App\Validators\TransactionValidator;
use App\Helpers\ResponseHelper;

class TransactionController extends AuthMiddleware
{
    private $service;
    private $validator;

    public function __construct()
    {
        parent::__construct(); 
        $this->service = new TransactionService();
        $this->validator = new TransactionValidator();
        AuthMiddleware::check();
    }

    public function withdraw()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $errors = $this->validator->validateWithdraw($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->service->withdraw($data['account_number'], $data['amount'], $data['pin']);

        return $result['success']
            ? ResponseHelper::success([], $result['message'])
            : ResponseHelper::error([], $result['message'], 400);
    }

    public function transfer()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $errors = $this->validator->validateTransfer($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->service->transfer($data['from_account'], $data['to_account'], $data['amount'], $data['pin'], $data['external_bank'] ?? null);

        return $result['success']
            ? ResponseHelper::success([], $result['message'])
            : ResponseHelper::error([], $result['message'], 400);
    }

    public function getHistory($accountNumber, $page = 1)
    {
        $result = $this->service->getTransactionHistory($accountNumber, $page);
        return ResponseHelper::success($result, 'Transaction history fetched');
    }

    public function deposit()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $validator = new TransactionValidator();
        $errors = $validator->validateDeposit($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->service->deposit($data['account_number'], $data['amount']);

        return $result['success']
            ? ResponseHelper::success([], $result['message'])
            : ResponseHelper::error([], $result['message'], 400);
    }
}