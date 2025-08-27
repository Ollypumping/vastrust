<?php
namespace App\Models;

use App\Core\Model;

class Admin extends Model
{

    public function register($data)
    {
        $sql = "INSERT INTO users (email, password, first_name, last_name, role) VALUES (:email, :password, :first_name, :last_name, :role)";
        return $this->execute($sql, $data);
    }
    
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users WHERE role = 'user'";
        return $this->query($sql);
    }

    public function getUserAccounts($userId)
    {
        $sql = "SELECT * FROM accounts WHERE user_id = ?";
        return $this->query($sql, [$userId]);
    }

    public function getAllAccounts(){
        $sql = "SELECT * FROM accounts";
        return $this->query($sql);
    }

    public function deactivateUser($userId)
    {
        $sql = "UPDATE users SET status = 'inactive' WHERE id = ?";
        return $this->execute($sql, [$userId]);
    }

    public function activateUser($userId)
    {
        $sql = "UPDATE users SET status = 'active' WHERE id = ?";
        return $this->execute($sql, [$userId]);
    }

    public function updateUser($userId, $data)
    {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }

        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        return $this->execute($sql, $params);
    }

    public function getAllTransactions()
    {
        $sql = "SELECT * FROM transactions ORDER BY created_at DESC";
        return $this->query($sql);
    }

    public function changeUserPassword($userId, $hashedPassword)
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->execute($sql, [$hashedPassword, $userId]);
    }

    public function changeUserPin($userId, $hashedPin)
    {
        $sql = "UPDATE users SET transaction_pin = ? WHERE id = ?";
        return $this->execute($sql, [$hashedPin, $userId]);
    }
}