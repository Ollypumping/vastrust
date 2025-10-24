<?php
namespace App\Services;

use App\Helpers\MailerHelper;
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
        $user = $this->user->findById($userId);
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
            'sender_account' => $accountNumber,
            'receiver_account' => null,
            'type' => 'withdrawal',
            'amount' => $amount,
            'description' => 'Withdrawal',
            'status' => 'success'
        ]);

        MailerHelper::sendWithdrawalNotification(
            $user['email'],
            $user['first_name'],
            $amount,
            $newBalance
        );



        return ['success' => true, 'message' => 'Withdrawal successful.'];
    }

    public function transfer($userId, $from, $to, $amount, $pin, $externalBank = null, $description = null)
    {
        $user = $this->user->fetchDetails($userId);
        //var_dump($user); exit;


        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if (empty($user['transaction_pin'])) {
            return ['success' => false, 'message' => 'Transaction PIN not set.'];
        }

        if (!password_verify($pin, $user['transaction_pin'])) {
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
        $toAccount = $this->account->getFullAccountDetails($to);
        if (!$toAccount && !$externalBank) {
            return ['success' => false, 'message' => 'Receiver account not found.'];
        }

        // Deduct from sender
        $newSenderBalance = $fromAccount['balance'] - $amount;
        $this->account->updateBalance($from, $newSenderBalance);

        // ✅ Send debit alert to sender (applies to both interbank and intrabank)
        MailerHelper::sendTransferNotification(
            $user['email'],
            $user['first_name'],
            $amount,
            $toAccount ? ($toAccount['first_name'] . ' ' . $toAccount['last_name']) : "Acct No: $to @ $externalBank",
            $newSenderBalance
        );


        //Add to beneficiary
        if(!$toAccount && $externalBank) {
            (new BeneficiaryService())->saveBeneficiary(
                $userId, $to, 'External Beneficiary', $externalBank);
        }
        else if ($toAccount) {
            (new BeneficiaryService())->saveBeneficiary(
                $userId, $to, $toAccount['first_name'] . ' ' . $toAccount['last_name']);
        }

        // Interbank: don't credit anyone
        if (!$toAccount && $externalBank) {
            $this->transaction->log([
                'sender_account' => $from,
                'receiver_account' => $to,
                'type' => 'transfer',
                'amount' => $amount,
                'description' => $description ?? ('Interbank transfer to ' . $externalBank),
                'status' => 'success',
                'external_bank' => $externalBank
            ]);

            return ['success' => true, 'message' => 'Interbank transfer initiated.',
            'data' => [
                'sender_name' => $user['first_name'] . ' ' . $user['last_name'],
                'amount' => $amount,
                'sender_account' => $from,
                'receiver_account' => $to
            ]];
        }

        // Intra-bank: credit recipient
        $newReceiverBalance = $toAccount['balance'] + $amount;
        $this->account->updateBalance($to, $newReceiverBalance);

        $this->transaction->log([
            'sender_account' => $from,
            'receiver_account' => $to,
            'type' => 'transfer',
            'amount' => $amount,
            'description' => $description ?? 'Intra-bank transfer',
            'status' => 'success',
            'external_bank' => null
        ]);

        // ✅ Send credit alert to receiver
        // MailerHelper::sendCreditAlertNotification(
        //     $toAccount['email'],
        //     $toAccount['first_name'] . ' ' . $toAccount['last_name'],
        //     $amount,
        //     $user['first_name'] . ' ' . $user['last_name'],
        //     $newReceiverBalance
        // );

        return ['success' => true, 'message' => 'Intra-bank transfer completed.',
            'data' => [
                'sender_name' => $user['first_name'] . ' ' . $user['last_name'],
                'receiver_name' => $toAccount['first_name'] . ' ' . $toAccount['last_name'],
                'amount' => $amount,
                'receiver_balance' => $newReceiverBalance,
                'sender_account' => $from,
                'receiver_account' => $to

            ]];

    }

    public function deposit($userId, $accountNumber, $amount)
    {
        
        $user = $this->user->findById($userId);
        $account = $this->account->getByAccountNumber($accountNumber);
        if (!$account) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        $newBalance = $account['balance'] + $amount;
        $this->account->updateBalance($accountNumber, $newBalance);

        $this->transaction->log([
            'sender_account' => null,
            'receiver_account' => $accountNumber,
            'type' => 'deposit',
            'amount' => $amount,
            'description' => 'Deposit',
            'status' => 'success',
            'external_bank' => null
        ]);

        MailerHelper::sendDepositNotification(
            $user['email'],
            $user['first_name'],
            $amount,
            $newBalance
        );

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