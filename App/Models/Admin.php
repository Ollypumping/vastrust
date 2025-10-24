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
        $sql = "SELECT 
                id, 
                CONCAT(first_name, ' ', last_name) AS full_name, 
                email, 
                status, 
                created_at 
            FROM users 
            WHERE role = 'user'
            ORDER BY created_at DESC
        ";

        return $this->query($sql);
    }

    public function getUserById($userId)
    {
        $sql = "SELECT 
                 
                CONCAT(first_name, ' ', last_name) AS full_name, 
                email, phone_number, account_number, first_name, last_name, dob, address, bvn,
                status, role
                created_at 
            FROM users 
            WHERE id = ? AND role = 'user'
        ";

        $result = $this->query($sql, [$userId]);
        return $result ? $result[0] : null;
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

       $sql = "
            SELECT t.*,
                CONCAT(sender.first_name, ' ', sender.last_name) AS sender_name,
                CONCAT(receiver.first_name, ' ', receiver.last_name) AS receiver_name
            FROM transactions t
            LEFT JOIN accounts sa ON t.sender_account = sa.account_number
            LEFT JOIN users sender ON sa.user_id = sender.id
            LEFT JOIN accounts ra ON t.receiver_account = ra.account_number
            LEFT JOIN users receiver ON ra.user_id = receiver.id
            ORDER BY t.created_at DESC
        ";
        return $this->query($sql);
    }

    public function getUserTransactions($userId)
    {
        $sql = "
            SELECT t.*, 
                CONCAT(sender.first_name, ' ', sender.last_name) AS sender_name,
                CONCAT(receiver.first_name, ' ', receiver.last_name) AS receiver_name
            FROM transactions t
            LEFT JOIN accounts sa ON t.sender_account = sa.account_number
            LEFT JOIN users sender ON sa.user_id = sender.id
            LEFT JOIN accounts ra ON t.receiver_account = ra.account_number
            LEFT JOIN users receiver ON ra.user_id = receiver.id
            WHERE sender.id = ? OR receiver.id = ?
            ORDER BY t.created_at DESC
        ";
        return $this->query($sql, [$userId, $userId]);
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