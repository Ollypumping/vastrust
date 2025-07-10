<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Validators\LoginValidator;
use App\Validators\RegisterValidator;
use App\Helpers\ResponseHelper;

class RegController
{
    private $service;
    private $rvalidator;
    private $lvalidator;

    public function __construct()
    {
        $this->service = new AuthService();
        $this->rvalidator = new RegisterValidator();
        $this->lvalidator = new LoginValidator();
    }

    public function register()
    {
        $data = $_POST;
        $files = $_FILES;

        $errors = $this->rvalidator->validate($data, $files);
        if (!empty($errors)) {
            return ResponseHelper::error($errors, 'Validation failed');
        }

        $result = $this->service->register($data, $files);
        if (!$result['success']) {
            return ResponseHelper::error([], $result['message'], 400);
        }

        return ResponseHelper::success($result['data'], 'Registration successful', 201);
    }

   public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        //$validator = new LoginValidator();
        $errors = $this->lvalidator->validate($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->service->login($data['email'], $data['password']);

        if ($result['success']) {
            return ResponseHelper::success($result['data'], $result['message']);
        }

        return ResponseHelper::error([], $result['message'], 401);
    }

    public function resetPassword()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';

        if (!$email) {
            return ResponseHelper::error(['email' => 'Email is required'], 'Validation failed');
        }

        $this->service->resetPassword($email);
        return ResponseHelper::success([], 'Reset password link sent (mock)');
    }

    public function verifyResetCode($email, $code)
    {
        $user = $this->user->findByEmail($email);
        if (!$user) return ['success' => false, 'message' => 'Invalid email.'];

        $isValid = $this->user->verifyCode($user['id'], $code);
        if (!$isValid) return ['success' => false, 'message' => 'Invalid or expired code.'];

        return ['success' => true, 'message' => 'Code verified.'];
    }

    public function updatePasswordAfterReset($email, $newPassword)
    {
        $user = $this->user->findByEmail($email);
        if (!$user) return ['success' => false, 'message' => 'Invalid user.'];

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->user->updatePassword($user['id'], $hashed);

        return ['success' => true, 'message' => 'Password updated successfully.'];
    }

}