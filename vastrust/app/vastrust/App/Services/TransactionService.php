<?php
namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;

class TransactionService
{
    private $account;
    private $transaction;

    public function __construct()
    {
        $this->account = new Account();
        $this->transaction = new Transaction();
    }

    public function withdraw($accountNumber, $amount)
    {
        $account = $this->account->getByAccountNumber($accountNumber);

        if (!$account) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        if ($account['balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient funds.'];
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

    public function transfer($from, $to, $amount)
    {
        if ($from === $to) {
            return ['success' => false, 'message' => 'Cannot transfer to the same account.'];
        }

        $fromAccount = $this->account->getByAccountNumber($from);
        $toAccount = $this->account->getByAccountNumber($to);

        if (!$fromAccount || !$toAccount) {
            return ['success' => false, 'message' => 'One or both accounts not found.'];
        }

        if ($fromAccount['balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient funds in sender account.'];
        }

        // Deduct from sender
        $this->account->updateBalance($from, $fromAccount['balance'] - $amount);

        // Add to receiver
        $this->account->updateBalance($to, $toAccount['balance'] + $amount);

        // Log transaction
        $this->transaction->log([
            'from_account' => $from,
            'to_account' => $to,
            'type' => 'transfer',
            'amount' => $amount,
            'description' => 'Intra-bank transfer',
            'status' => 'success'
        ]);

        return ['success' => true, 'message' => 'Transfer successful.'];
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