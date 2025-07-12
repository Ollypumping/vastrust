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




//    public function profile($userId)
//    {
//        JwtMiddleware::check();
//        $result = $this->authService->getProfile($userId);
//
//        if ($result) {
//            return ResponseHelper::success($result, "Profile retrieved successfully");
//        }
//
//        return ResponseHelper::error([], "User not found", 404);
//    }

    public function profile($userId)
    {
        JwtMiddleware::check();

        // Get decoded JWT from session
        $decodedJwt = $_SESSION['decoded'] ?? null;

        if (!$decodedJwt) {
            return ResponseHelper::error([], "JWT payload missing in session", 500);
        }

        // Extract user_id from decoded JWT
        $jwtUserId = $decodedJwt->user_id ?? null;

        $result = $this->authService->getProfile($jwtUserId);
        $sessionId = $_SESSION['user_id'];


        if ($result) {
            return ResponseHelper::success(
                [
                    'profile' => $result,
                ],
                "Profile retrieved successfully"
            );
        }

        return ResponseHelper::error([
            'profile' => null,
        ], "User not found", 404);
    }



    public function changePassword($userId)
    {
        JwtMiddleware::check();
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
            // Generate JWT
            $env = parse_ini_file(__DIR__ . '/../../.env');
            $secret = $env['JWT_SECRET'];
            $payload = [
                'user_id' => $result['data']['user_id'],
                'email' => $result['data']['email'],
                'exp' => time() + 60*60*24 // 1 day
            ];
            $jwt = JWT::encode($payload, $secret, 'HS256');
            return ResponseHelper::success([
                'token' => $jwt,
                'user' => $result['data']
            ], $result['message']);
        }

        return ResponseHelper::error([], $result['message'], 401);
    }


}