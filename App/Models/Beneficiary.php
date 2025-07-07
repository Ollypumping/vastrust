<?php

namespace App\Models;
use config\database;

class Beneficiary
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function save($data)
    {
        $sql = "INSERT INTO beneficiaries (user_id, account_number, account_name, external_bank)
                VALUES (:user_id, :account_number, :account_name, :external_bank)";
        $this->db->query($sql);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':account_number', $data['account_number']);
        $this->db->bind(':account_name', $data['account_name']);
        $this->db->bind(':external_bank', $data['external_bank']);
        return $this->db->execute();
    }

    public function getByUser($userId)
    {
        $this->db->query("SELECT * FROM beneficiaries WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function delete($id, $userId)
    {
        $this->db->query("DELETE FROM beneficiaries WHERE id = :id AND user_id = :user_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
}