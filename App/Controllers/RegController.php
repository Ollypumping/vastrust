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
}