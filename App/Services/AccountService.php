<?php
namespace App\Services;

use App\Models\Account;
use App\Helpers\AcctNumGenerator;

class AccountService
{
    private $account;

    public function __construct()
    {
        $this->account = new Account();
    }

    public function create($userId, $accountType = 'savings')
    {
        // To generate a unique 10-digit account number
        $accountNumber = AcctNumGenerator::generate();

        // To check uniqueness in the DB
        while ($this->account->getByAccountNumber($accountNumber)) {
            $accountNumber = AcctNumGenerator::generate();
        }

        $data = [
            'user_id' => $userId,
            'account_number' => $accountNumber,
            'balance' => 0.00,
            'account_type' => $accountType
        ];

        $success = $this->account->create($data);

        return $success
            ? [
                'success' => true,
                'message' => 'Account created successfully.',
                'data' => ['account_number' => $accountNumber, 'account_type' => $accountType, 'balance' => 0.00]
            ]
            : [
                'success' => false,
                'message' => 'Failed to create account.'
            ];
    }

    public function getBalance($accountNumber)
    {
        $account = $this->account->getByAccountNumber($accountNumber);
        return $account ? $account['balance'] : false;
    }

    public function getAllUserAccounts($userId)
    {
        return $this->account->getByUserId($userId);
    }
}