<?php
namespace App\Models;

use App\Core\Model;

class ResetCode extends Model
{
    public function saveCode($userId, $email, $otp)
    {
        $sql = "INSERT INTO reset_codes (user_id, email, code, expires_at)
                VALUES (:user_id, :email, :code, DATE_ADD(NOW(), INTERVAL 10 MINUTE))";

        return $this->execute($sql, [
            'user_id' => $userId,
            'email' => $email,
            'code' => $otp
        ]);
    }

    public function verifyCode($email, $code)
    {
        $sql = "SELECT * FROM reset_codes
                WHERE email = :email AND code = :code AND expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT 1";

        return $this->query($sql, [
            'email' => $email,
            'code' => $code
        ], true); // true = fetch single row
    }

    public function invalidateCode($email)
    {
        $sql = "DELETE FROM reset_codes WHERE email = :email";

        return $this->execute($sql, ['email' => $email]);
    }
}
