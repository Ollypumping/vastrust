<?php
namespace App\Models;

use App\Core\Model;
use config\database;
use PDO;

class User extends Model {

    public function create($data) {
        $sql = "INSERT INTO users (
                    email, password, first_name, last_name, account_number, passport_photo, dob, occupation, 
                    address, phone_number, bvn -- transaction_pin
                    -- nok_first_name, nok_last_name, nok_phone_number, nok_address
                ) VALUES (
                    :email, :password, :first_name, :last_name, :account_number, :passport_photo, :dob, :occupation, 
                    :address, :phone_number, :bvn -- :transaction_pin
                    -- :nok_first_name, :nok_last_name, :nok_phone_number, :nok_address
                )";

        return $this->execute($sql, $data);
    }

    public function updatePassportPhoto($userId, $photoName)
    {
        $stmt = $this->conn->prepare("UPDATE users SET passport_photo = ? WHERE id = ?");
        return $stmt->execute([$photoName, $userId]);
    }


    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->query($sql, ['email' => $email], true);
    }

    public function findById($id) {
         $sql = "
            SELECT 
                u.id, u.email, u.first_name, u.last_name, 
                u.account_number, a.balance, u.role
            FROM users u
            LEFT JOIN accounts a ON u.id = a.user_id
            WHERE u.id = :id
        ";

        return $this->query($sql, ['id' => $id], true);

    }


    public function getLastInsertId(){
        return $this->conn->lastInsertId();  // PDO built-in
    }


    public function updateAccountNumber($userId, $accountNumber) {
        $sql = "UPDATE users SET account_number = :account_number WHERE id = :id";
        return $this->execute($sql, [
            'account_number' => $accountNumber,
            'id' => $userId
        ]);
    }

    public function updatePin($userId, $hashedPin)
    {
        $sql = "UPDATE users SET transaction_pin = :pin WHERE id = :id";
        return $this->execute($sql, [
            'pin' => $hashedPin,
            'id' => $userId
        ]);
    }

    public function fetchPin($userId) {
        $sql = "SELECT transaction_pin FROM users WHERE id = :id";
        return $this->query($sql, ['id' => $userId], true);
    }

    public function updatePassword($userId, $newHash)
    {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        return $this->execute($sql, [
            'password' => $newHash,
            'id' => $userId
        ]);
    }

    public function fetchDetails($userId) {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->query($sql, ['id' => $userId], true);
    }

    public function updatePasswordByEmail($email, $newHash)
    {
        $sql = "UPDATE users SET password = :password WHERE email = :email";
        return $this->execute($sql, [
            'password' => $newHash,
            'email' => $email
        ]);
    }

    public function updatePinByEmail($email, $newPin)
    {
        $sql = "UPDATE users SET transaction_pin = :pin WHERE email = :email";
        return $this->execute($sql, [
            'pin' => $newPin,
            'email' => $email
        ]);
    }

}