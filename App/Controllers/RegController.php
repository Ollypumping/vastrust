<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\VerificationService;
use App\Validators\LoginValidator;
use App\Validators\RegisterValidator;
use App\Helpers\ResponseHelper;

class RegController
{
    private $service;
    private $verificationService;
    private $rvalidator;
    private $lvalidator;

    public function __construct()
    {
        $this->service = new AuthService();
        $this->verificationService = new VerificationService();
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

        $this->service->sendVerificationCode($result['user_id'], $email, 'register');

        return ResponseHelper::success($result['data'], 'Registration successful. Verification email sent.', 201);
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

        $result = $this->service->resetPassword($email);

        if ($result['success']) {
            return ResponseHelper::success([], $result['message']);
        } else {
            return ResponseHelper::error([], $result['message']);
        }
    }

    public function verifyCode()    // For registration
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'] ?? null;
        $code = $data['code'] ?? null;

        return $this->verificationService->verify($email, $code);
    }


    public function updatePasswordAfterReset()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data['email'] ?? null;
        $otp = $data['otp'] ?? null;
        $new_password = $data['new_password'] ?? null;
        $confirm_password = $data['confirm_password'] ?? null;

        if (!$email || !$otp || !$new_password) {
            return ResponseHelper::error([], 'Missing fields');
        }

        if ($new_password !== $confirm_password) {
            return ResponseHelper::error([], 'Passwords do not match');
        }

        $result = $this->service->updatePasswordAfterVerification($email, $otp, $new_password);

        if ($result['success']) {
            return ResponseHelper::success([], $result['message']);
        } else {
            return ResponseHelper::error([], $result['message']);
        }
    }

}