<?php
namespace App\Middlewares;

use App\Helpers\ResponseHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    public static function check()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (!isset($headers['Authorization'])) {
            ResponseHelper::error([], 'Authorization header missing', 401);
            exit;
        }
        $authHeader = $headers['Authorization'];
        if (strpos($authHeader, 'Bearer ') !== 0) {
            ResponseHelper::error([], 'Invalid authorization header', 401);
            exit;
        }
        $jwt = substr($authHeader, 7);
        $env = parse_ini_file(__DIR__ . '/../../.env');
        $secret = $env['JWT_SECRET'];
        try {
            $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
            $_SESSION['user_id'] = $decoded->user_id;
            // Optionally, set more user info in session
        } catch (\Exception $e) {
            ResponseHelper::error([], 'Invalid or expired token', 401);
            exit;
        }
    }
} 