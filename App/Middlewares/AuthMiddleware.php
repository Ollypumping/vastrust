<?php
namespace App\Middlewares;

use App\Models\User;
use App\Helpers\ResponseHelper;

class AuthMiddleware
{
    public function __construct()
    {
        $publicRoutes = [
            '/api/register',
            '/api/reset-password'
        ];

        $uri = $_SERVER['REQUEST_URI'];
        $uri = strtok($uri, '?'); // remove query params

        if (!in_array($uri, $publicRoutes)) {
            self::check();
        }

        $this->check();


    }

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

        
        $_SESSION['user_id'] = $user['id'];
    }

    private static function unauthorized($message)
    {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        ResponseHelper::error([], $message, 401);
    }
}