<?php
namespace App\Middlewares;

use App\Models\User;
use App\Helpers\ResponseHelper;

class AuthMiddleware
{

    private const API_USERNAME = 'vastrust_api';
    private const API_PASSWORD = '123456789';

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


    }

    public static function check()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            self::unauthorized("Authentication required.");
            exit;
        }

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Compare with internal API credentials
        if ($username !== self::API_USERNAME || $password !== self::API_PASSWORD) {
            self::unauthorized("Invalid credentials.");
            exit;
        }

        
    }

    private static function unauthorized($message)
    {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        ResponseHelper::error([], $message, 401);
    }
}