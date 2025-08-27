<?php
namespace App\Middlewares;

use App\Models\User;
use App\Helpers\ResponseHelper;

class RoleMiddleware
{
    protected $user;

    public function __construct($userId)
    {
        $userModel = new User();
        $this->user = $userModel->findById($userId);

        if (!$this->user) {
            ResponseHelper::error([], "User not found", 404);
            exit;
        }

        if ($this->user['role'] !== 'admin') {
            ResponseHelper::error([], "Unauthorized: Admin access required", 403);
            exit;
        }
    }

    public function getUser()
    {
        return $this->user;
    }
}
