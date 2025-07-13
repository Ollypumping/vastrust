<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Middlewares\JwtMiddleware;
use App\Services\AuthService;
use App\Validators\RegisterValidator;
use App\Validators\LoginValidator;
use App\Validators\PasswordValidator;
use App\Helpers\ResponseHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private $authService;

    public function __construct()
    {
//        parent::__construct();
        $this->authService = new AuthService();
    }




    public function profile($userId)
    {
        JwtMiddleware::check();
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


    }

}