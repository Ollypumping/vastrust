<?php
namespace App\Validators;

use App\Helpers\BankHelper;
class TransactionValidator
{
    public function validateWithdraw($data)
    {
        $errors = [];

        if (empty($data['account_number'])) {
            $errors['account_number'] = 'Account number is required.';
        }

        if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'A valid withdrawal amount is required.';
        }

        if (empty($data['pin']) || !preg_match('/^\d{4}$/', $data['pin'])) {
            $errors['pin'] = 'Transaction PIN is required and must be 4 digits.';
        }

        return $errors;
    }

    public function validateTransfer($data)
    {
        $errors = [];

        if (empty($data['from_account'])) {
            $errors['from_account'] = 'Sender account number is required.';
        }

        if (empty($data['to_account'])) {
            $errors['to_account'] = 'Receiver account number is required.';
        }

        if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'A valid transfer amount is required.';
        }

        if (empty($data['pin']) || !preg_match('/^\d{4}$/', $data['pin'])) {
            $errors['pin'] = 'Transaction PIN is required and must be 4 digits.';
        }

        //Validate if it an inter bank transfer
        if(!empty($data['external_bank'])){
            $validBanks = BankHelper::getBanks();
            if (!in_array($data['external_bank'], $validBanks)) {
                $errors['external_bank'] = 'Invalid bank name.';
            }
        }

        return $errors;
    }

    public function validateDeposit($data)
    {
        $errors = [];

        if (empty($data['account_number'])) {
            $errors['account_number'] = 'Account number is required.';
        }

        if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'A valid deposit amount is required.';
        }

        return $errors;
    }
}