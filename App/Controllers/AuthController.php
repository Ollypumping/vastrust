<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Services\AuthService;
use App\Validators\RegisterValidator;
use App\Validators\LoginValidator;
use App\Validators\PasswordValidator;
use App\Helpers\ResponseHelper;

class AuthController extends AuthMiddleware
{
    private $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }




    public function profile($userId)
    {
        $result = $this->authService->getProfile($userId);

        if ($result) {
            return ResponseHelper::success($result, "Profile retrieved successfully");
        }

        return ResponseHelper::error([], "User not found", 404);
    }

    public function changePassword($userId)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $validator = new PasswordValidator();
        $errors = $validator->validateChange($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->authService->changePassword($userId, $data['old_password'], $data['new_password']);

        if ($result['success']) {
            return ResponseHelper::success([], $result['message']);
        }

        return ResponseHelper::error([], $result['message']);
    }

    public function changePin($userId)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $validator = new PasswordValidator();
        $errors = $validator->validatePinChange($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->authService->changePin($userId, $data['old_pin'], $data['new_pin']);

        if ($result['success']) {
            return ResponseHelper::success([], $result['message']);
        }

        return ResponseHelper::error([], $result['message']);
    }

    public function resetPin()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';

        if (!$email) {
            return ResponseHelper::error(['email' => 'Email is required'], 'Validation failed');
        }

        $result = $this->authService->resetPin($email);

        if ($result['success']) {
            return ResponseHelper::success([], $result['message']);
        } else {
            return ResponseHelper::error([], $result['message']);
        }
    }

    public function updatePinAfterReset()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data['email'] ?? null;
        $otp = $data['otp'] ?? null;
        $new_pin = $data['new_pin'] ?? null;
        $confirm_pin = $data['confirm_pin'] ?? null;

        if (!$email || !$otp || !$new_pin) {
            return ResponseHelper::error([], 'Missing required fields');
        }

        if ($new_pin !== $confirm_pin) {
            return ResponseHelper::error([], 'PINs do not match');
        }

        $result = $this->authService->updatePinAfterVerification($email, $otp, $new_pin);

        if ($result['success']) {
            return ResponseHelper::success([], $result['message']);
        } else {
            return ResponseHelper::error([], $result['message']);
        }
    }

    public function setupTransactionPin()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $email = $data['email'] ?? '';
        $pin = $data['transaction_pin'] ?? '';
        $confirmPin = $data['confirm_pin'] ?? '';

        if (!$email || !$pin || !$confirmPin) {
            return ResponseHelper::error([], 'All fields are required', 422);
        }

        if (!preg_match('/^\d{4}$/', $pin)) {
            return ResponseHelper::error('Transaction PIN must be a 4-digit number.');
        }

        if ($pin !== $confirmPin) {
            return ResponseHelper::error([], 'PINs do not match', 422);
        }

        $result = $this->service->setupTransactionPin($email, $pin);

        if ($result['success']) {
            return ResponseHelper::success([], $result['message']);
        }

        return ResponseHelper::error([], $result['message'], 400);
    }




}