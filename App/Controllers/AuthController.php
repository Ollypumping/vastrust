<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Services\AuthService;
use App\Validators\RegisterValidator;
use App\Validators\LoginValidator;
use App\Validators\PasswordValidator;
use App\Helpers\ResponseHelper;

class AuthController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
        AuthMiddleware::check();
    }



    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $validator = new LoginValidator();
        $errors = $validator->validate($data);

        if (!empty($errors)) {
            return ResponseHelper::error($errors, "Validation failed", 422);
        }

        $result = $this->authService->login($data['email'], $data['password']);

        if ($result['success']) {
            return ResponseHelper::success($result['data'], $result['message']);
        }

        return ResponseHelper::error([], $result['message'], 401);
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


}