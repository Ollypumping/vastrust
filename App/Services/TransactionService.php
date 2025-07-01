<?php
namespace App\Services;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\BeneficiaryService;

class TransactionService
{
    private $account;
    private $transaction;

    public function __construct()
    {
        $this->user = new User;
        $this->account = new Account();
        $this->transaction = new Transaction();
    }

    public function withdraw($accountNumber, $amount, $pin)
    {
        $user = $this->user->findById($_SESSION['user_id']);
        $account = $this->account->getByAccountNumber($accountNumber);

        if (!$account) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        if ($account['balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient funds.'];
        }

        if(!$user || !password_verify($pin, $user['transaction_pin'])) {
            return ['success' => false, 'message' => 'Invalid transaction PIN.'];
        }

        $newBalance = $account['balance'] - $amount;
        $this->account->updateBalance($accountNumber, $newBalance);

        $this->transaction->log([
            'from_account' => $accountNumber,
            'to_account' => null,
            'type' => 'withdrawal',
            'amount' => $amount,
            'description' => 'Withdrawal',
            'status' => 'success'
        ]);

        return ['success' => true, 'message' => 'Withdrawal successful.'];
    }

    public function transfer($from, $to, $amount, $externalBank = null, $pin)
    {
        $user = $this->user->findById($_SESSION['user_id']);

        if (!$user || !password_verify($pin, $user['transaction_pin'])) {
            return ['success' => false, 'message' => 'Invalid transaction PIN.'];
        }

        $fromAccount = $this->account->getByAccountNumber($from);

        if (!$fromAccount) {
            return ['success' => false, 'message' => 'Sender account not found.'];
        }

        if ($from === $to) {
            return ['success' => false, 'message' => 'Cannot transfer to the same account.'];
        }

        if ($fromAccount['balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient funds.'];
        }

        // Check if it's interbank
        $toAccount = $this->account->getByAccountNumber($to);
        if (!$toAccount && !$externalBank) {
            return ['success' => false, 'message' => 'Receiver account not found.'];
        }

        // Deduct from sender
        $this->account->updateBalance($from, $fromAccount['balance'] - $amount);

        //Add to beneficiary
        if(!$toAccount && $externalBank) {
            (new BeneficiaryService())->saveBeneficiary(
                $_SESSION['user_id'], $to, 'External Beneficiary', $externalBank);
        }
        else if ($toAccount) {
            (new BeneficiaryService())->saveBeneficiary(
                $_SESSION['user_id'], $to, $toAccount['account_name']);
        }

        // Interbank: don't credit anyone
        if (!$toAccount && $externalBank) {
            $this->transaction->log([
                'from_account' => $from,
                'to_account' => null,
                'type' => 'transfer',
                'amount' => $amount,
                'description' => 'Interbank transfer to ' . $externalBank,
                'status' => 'success',
                'external_bank' => $externalBank
            ]);

            return ['success' => true, 'message' => 'Interbank transfer initiated.'];
        }

        // Intra-bank: credit recipient
        $this->account->updateBalance($to, $toAccount['balance'] + $amount);

        $this->transaction->log([
            'from_account' => $from,
            'to_account' => $to,
            'type' => 'transfer',
            'amount' => $amount,
            'description' => 'Intra-bank transfer',
            'status' => 'success',
            'external_bank' => null
        ]);

        return ['success' => true, 'message' => 'Intra-bank transfer completed.'];

    }

    public function deposit($accountNumber, $amount)
    {
        $account = $this->account->getByAccountNumber($accountNumber);
        if (!$account) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        $newBalance = $account['balance'] + $amount;
        $this->account->updateBalance($accountNumber, $newBalance);

        $this->transaction->log([
            'from_account' => null,
            'to_account' => $accountNumber,
            'type' => 'deposit',
            'amount' => $amount,
            'description' => 'Deposit',
            'status' => 'success'
        ]);

        return ['success' => true, 'message' => 'Deposit successful.'];
    }

    public function getTransactionHistory($accountNumber, $page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $history = $this->transaction->getByAccount($accountNumber, $limit, $offset);

        return [
            'transactions' => $history,
            'page' => $page,
            'limit' => $limit
        ];
    }
}