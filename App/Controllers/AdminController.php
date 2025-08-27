<?php
namespace App\Controllers;

use App\Middlewares\RoleMiddleware;
use App\Models\Admin;
use App\Helpers\ResponseHelper;

class AdminController extends RoleMiddleware
{
    private $adminModel;

    public function __construct($adminId)
    {
        parent::__construct($adminId); // Role check happens here
        $this->adminModel = new Admin();
    }

    public function getAllUsers()
    {
        $users = $this->adminModel->getAllUsers();
        return ResponseHelper::success($users);
    }

    public function getUserAccounts($userId)
    {
        $accounts = $this->adminModel->getUserAccounts($userId);
        return ResponseHelper::success($accounts);
    }

    public function deactivateUser($userId)
    {
        $this->adminModel->deactivateUser($userId);
        return ResponseHelper::success([], "User account deactivated.");
    }
    
    public function activateUser($userId)
    {
        $this->adminModel->activateUser($userId);
        return ResponseHelper::success([], "User account activated.");
    }

    public function updateUser($userId, $data)
    {
        $this->adminModel->updateUser($userId, $data);
        return ResponseHelper::success([], "User details updated.");
    }

    public function getAllTransactions()
    {
        $transactions = $this->adminModel->getAllTransactions();
        return ResponseHelper::success($transactions);
    }

    public function changeUserPassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->adminModel->changeUserPassword($userId, $hashedPassword);
        return ResponseHelper::success([], "User password updated successfully.");
    }

    public function changeUserPin($userId, $newPin)
    {
        $hashedPin = password_hash($newPin, PASSWORD_DEFAULT);
        $this->adminModel->changeUserPin($userId, $hashedPin);
        return ResponseHelper::success([], "User transaction PIN updated successfully.");
    }
}