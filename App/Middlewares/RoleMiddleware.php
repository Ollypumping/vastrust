<?php
namespace App\Middlewares;

use App\Models\User;
use App\Helpers\ResponseHelper;

class RoleMiddleware
{
    public static function checkAdmin()
    {
        if (!isset($_SESSION['user_id'])) {
            ResponseHelper::error([], 'Unauthorized. Please log in.', 401);
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user || ($user['role'] ?? '') !== 'admin') {
            ResponseHelper::error([], 'Access denied. Admin only.', 403);
            exit;
        }
    }
}