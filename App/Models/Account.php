<?php
namespace App\Models;

use App\Core\Model;
use config\Database;
use PDO;

class Account extends Model {

    public function create($data) {
        $sql = "INSERT INTO accounts (user_id, account_number, balance, account_type)
                VALUES (:user_id, :account_number, :balance, :account_type)";
        return $this->execute($sql, $data);
    }

    public function getByUserId($user_id) {
        $sql = "SELECT * FROM accounts WHERE user_id = :user_id";
        return $this->query($sql, ['user_id' => $user_id]);
    }

    public function getByAccountNumber($account_number) {
        $sql = "SELECT * FROM accounts WHERE account_number = :account_number";
        return $this->query($sql, ['account_number' => $account_number], true);
    }

    public function updateBalance($account_number, $new_balance) {
        $sql = "UPDATE accounts SET balance = :balance WHERE account_number = :account_number";
        return $this->execute($sql, [
            'balance' => $new_balance,
            'account_number' => $account_number
        ]);
    }

    public function getBalance($account_number) {
        $sql = "SELECT balance FROM accounts WHERE account_number = :account_number";
        $result = $this->query($sql, ['account_number' => $account_number], true);
        return $result ? $result['balance'] : false;
    }
}