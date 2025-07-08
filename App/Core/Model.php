<?php
namespace App\Core;

use config\database;
use PDO;

class Model
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    protected function query($sql, $params = [], $single = false)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $single ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function execute($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    protected function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }
}
