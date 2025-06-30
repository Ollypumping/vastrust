<?php
namespace App\Middlewares;

use App\Models\User;
use App\Helpers\ResponseHelper;

class AuthMiddleware
{
    public static function check()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            self::unauthorized("Authentication required.");
            exit;
        }

        $email = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            self::unauthorized("Invalid credentials.");
            exit;
        }

        // Attach user ID for use in controller
        $_SESSION['user_id'] = $user['id'];
    }

    private static function unauthorized($message)
    {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        ResponseHelper::error([], $message, 401);
    }
}