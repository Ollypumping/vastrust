<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Validators\LoginValidator;
use App\Helpers\ResponseHelper;

class AdminAuthController
{
    private $service;
    private $lvalidator;


    public function __construct()
    {
        $this->service = new AuthService();
        $this->lvalidator = new LoginValidator();
    }

    public function register()
    {
        $data = $_POST;
        $errors = $this->lvalidator->validate($data);
        if (!empty($errors)) {
            return ResponseHelper::error($errors, 'Validation failed');
        }

        $result = $this->service->registerAdmin($data);
        if (!$result['success']) {
            return ResponseHelper::error([], $result['message'], 400);
        }

        return ResponseHelper::success([], 'Admin Registration successful.', 201);
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
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
}