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
        return $this->beneficiary->save([
            'user_id' => $userId,
            'account_number' => $accountNumber,
            'account_name' => $accountName,
            'external_bank' => $externalBank
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