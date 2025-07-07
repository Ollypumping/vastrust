<?php
namespace App\Models;

use config\database;
use PDO;

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

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

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}