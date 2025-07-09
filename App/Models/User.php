<?php
namespace App\Models;

use App\Core\Model;
use config\database;
use PDO;

class User extends Model {

    public function create($data) {
        $sql = "INSERT INTO users (
                    email, password, first_name, last_name, account_number, passport_photo, age, occupation, 
                    address, phone_number, bvn, transaction_pin
                    -- nok_first_name, nok_last_name, nok_phone_number, nok_address
                ) VALUES (
                    :email, :password, :first_name, :last_name, :account_number, :passport_photo, :age, :occupation, 
                    :address, :phone_number, :bvn, :transaction_pin
                    -- :nok_first_name, :nok_last_name, :nok_phone_number, :nok_address
                )";

        return $this->execute($sql, $data);
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->query($sql, ['email' => $email], true);
    }

    public function findById($id) {
         $sql = "
            SELECT 
                u.id, u.email, u.first_name, u.last_name, 
                u.account_number, a.balance
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
}