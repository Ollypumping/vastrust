<?php


namespace App\Models;
use App\Core\Model;
use config\database;

class Beneficiary extends Model
{

    public function save($data)
    {
        $sql = "INSERT INTO beneficiaries (user_id, account_number, account_name, external_bank)
                VALUES (:user_id, :account_number, :account_name, :external_bank)";
        
        return $this->execute($sql, $data);
    }

    public function getByUser($userId)
    {
        $sql = "SELECT * FROM beneficiaries WHERE user_id = :user_id";
        return $this->query($sql, ['user_id' => $userId]);
    }

    public function delete($id, $userId)
    {
        $sql = "DELETE FROM beneficiaries WHERE id = :id AND user_id = :user_id";
        return $this->execute($sql, [
            'id' => $id,
            'user_id' => $userId
        ]);

    }
}