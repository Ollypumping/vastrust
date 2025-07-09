<?php
namespace App\Models;

use App\Core\Model;
use config\database;
use PDO;

class User extends Model {

    public function create($data) {
        $sql = "INSERT INTO users (
                    email, password, first_name, last_name, passport_photo, age, occupation, 
                    address, phone_number, bvn, transaction_pin,
                    nok_first_name, nok_last_name, nok_phone_number, nok_address
                ) VALUES (
                    :email, :password, :first_name, :last_name, :passport_photo, :age, :occupation, 
                    :address, :phone_number, :bvn, :transaction_pin,
                    :nok_first_name, :nok_last_name, :nok_phone_number, :nok_address
                )";

        return $this->execute($sql, $data);
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->query($sql, ['email' => $email], true);
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->query($sql, ['id' => $id], true);
    }

    public function getLastInsertId(){
        return $this->conn->lastInsertId();  // PDO built-in
    }
}