<?php
namespace App\Models;

use Database;
use PDO;

class Transaction {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function log($data) {
        $sql = "INSERT INTO transactions (
                    from_account, to_account, type, amount, description, status
                ) VALUES (
                    :from_account, :to_account, :type, :amount, :description, :status
                )";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function getByAccount($account_number, $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM transactions 
                WHERE from_account = :account_number OR to_account = :account_number
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':account_number', $account_number);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}