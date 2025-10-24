<?php

namespace App\Services;

use App\Models\Beneficiary;

class BeneficiaryService
{
    private $beneficiary;

    public function __construct()
    {
        $this->beneficiary = new Beneficiary();
    }

    public function saveBeneficiary($userId, $accountNumber, $accountName, $externalBank = null)
    {
        // Check if beneficiary already exists for this user
        $existing = $this->beneficiary->findByUserAndAccount($userId, $accountNumber);

        if ($existing) {
            // Optional: return a message or silently skip
            return ['success' => false, 'message' => 'Beneficiary already exists.'];
        }

        // Save new beneficiary
        return $this->beneficiary->save([
            'user_id' => $userId,
            'account_number' => $accountNumber,
            'account_name' => $accountName,
            'external_bank' => $externalBank
        ]);
    }

    public function updateBeneficiary($userId, $beneficiaryId, $accountName, $externalBank = null)
    {
        // you can only edit account_name & external_bank
        $sql = "UPDATE beneficiaries 
                SET account_name = :account_name, external_bank = :external_bank 
                WHERE id = :id AND user_id = :user_id";
        return $this->beneficiary->execute($sql, [
            'account_name' => $accountName,
            'external_bank' => $externalBank,
            'id' => $beneficiaryId,
            'user_id' => $userId
        ]);
    }


    public function getBeneficiaries($userId)
    {
        return $this->beneficiary->getByUser($userId);
    }

    public function deleteBeneficiary($id, $userId)
    {
        return $this->beneficiary->delete($id, $userId);
    }
}