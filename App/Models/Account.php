<?php
namespace App\Models;

use config\Database;
use PDO;

class Account {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function create($data) {
        $sql = "INSERT INTO accounts (user_id, account_number, balance, account_type)
                VALUES (:user_id, :account_number, :balance, :account_type)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM accounts WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAccountNumber($account_number) {
        $stmt = $this->conn->prepare("SELECT * FROM accounts WHERE account_number = :account_number");
        $stmt->execute(['account_number' => $account_number]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBalance($account_number, $new_balance) {
        $stmt = $this->conn->prepare("UPDATE accounts SET balance = :balance WHERE account_number = :account_number");
        return $stmt->execute([
            'balance' => $new_balance,
            'account_number' => $account_number
        ]);
    }
}