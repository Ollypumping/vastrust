<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Validators\RegisterValidator;
use App\Helpers\ResponseHelper;

class RegController
{
    private $service;
    private $validator;

    public function __construct()
    {
        $this->service = new AuthService();
        $this->validator = new RegisterValidator();
    }

    public function register()
    {
        $data = $_POST;
        $files = $_FILES;

        $errors = $this->validator->validate($data, $files);
        if (!empty($errors)) {
            return ResponseHelper::error($errors, 'Validation failed');
        }

        $result = $this->service->register($data, $files);
        return ResponseHelper::success($result, 'Registration successful', 201);
    }

    // public function login()
    // {
    //     return ResponseHelper::success([], 'Login successful (handled via Basic Auth)');
    // }

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
}