<?php
namespace App\Services;

use App\Helpers\MailerHelper;
use App\Core\Model;
use App\Services\AccountService;
use App\Models\User;
use App\Helpers\ResponseHelper;
use App\Models\Verification;
use App\Services\VerificationService;

class AuthService
{
    private $user;
    private $accountService;
    private $verificationService;

    public function __construct()
    {
        $this->user = new User();
        $this->accountService = new AccountService();
        $this->verificationService = new VerificationService();
    }

    public function register($data, $file)
    {
        if ($this->user->findByEmail($data['email'])) {
            return [
                'success' => false,
                'message' => 'A user with this email already exists.'
            ];
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        //$data['transaction_pin'] = password_hash($data['transaction_pin'], PASSWORD_DEFAULT);

        $photoName = null;

        if (!empty($file['passport_photo']['name'])) {
            $photoName = time() . '_' . $file['passport_photo']['name'];
            $targetPath = _DIR_ . '/../../storage/uploads/' . $photoName;
            move_uploaded_file($file['passport_photo']['tmp_name'], $targetPath);
        }

        $data['passport_photo'] = $photoName;

        $userCreated = $this->user->create([
            'email' => $data['email'],
            'password' => $data['password'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'account_number' => $data['account_number'] ?? null,
            'passport_photo' => $data['passport_photo'],
            'dob' => $data['dob'],
            'occupation' => $data['occupation'] ?? null,
            'address' => $data['address'],
            'phone_number' => $data['phone_number'],
            'bvn' => $data['bvn'],
            //'transaction_pin' => $data['transaction_pin']
            // 'nok_first_name' => $data['nok_first_name'],
            // 'nok_last_name' => $data['nok_last_name'],
            // 'nok_phone_number' => $data['nok_phone_number'],
            // 'nok_address' => $data['nok_address']
        ]);
        if (!$userCreated) {
            return [
                'success' => false,
                'message' => 'User registration failed.'
            ];
        }
        $userId = $this->user->getLastInsertId();
        $account = $this->accountService->create($userId, $data['account_type'] ?? 'savings');
        if ($account['success']) {
            $this->user->updateAccountNumber($userId, $account['data']['account_number']);
        }

        return [
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'user_id' => $userId,
                'email' => $data['email'],
                'account' => $account
            ]
        ];
    }

    public function login($email, $password)
    {
        $user = $this->user->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }

        MailerHelper::sendLoginNotification($email, $user['first_name']);

        return [
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'full_name' => $user['first_name'] . ' ' . $user['last_name']
            ]
        ];
    }

    public function getProfile($userId)
    {
        return $this->user->findById($userId);
    }

    public function changePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->user->fetchDetails($userId);

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Old password is incorrect.'];
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updated = $this->user->updatePassword($userId, $newHash);

        return $updated
            ? ['success' => true, 'message' => 'Password changed successfully.']
            : ['success' => false, 'message' => 'Password update failed.'];
    }

    

    public function resetPassword($email)
    {
        $user = $this->user->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found.'
            ];
        }

        //$verification = new VerificationService();
        return $this->verificationService->sendCode($user['id'], $email, 'reset_password');
    }

    public function resetPin($email)
    {
        $user = $this->user->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found.'
            ];
        }

        //$verification = new VerificationService();
        return $this->verificationService->sendCode($user['id'], $email, 'reset_pin');
    }

    public function changePin($userId, $oldPin, $newPin)
    {
        $user = $this->user->fetchDetails($userId);

        if (!$user || !password_verify($oldPin, $user['transaction_pin'])) {
            return ['success' => false, 'message' => 'Old PIN is incorrect.'];
        }

        $newHash = password_hash($newPin, PASSWORD_DEFAULT);
        $updated = $this->user->updatePin($userId, $newHash);

        return $updated
            ? ['success' => true, 'message' => 'Transaction PIN changed successfully.']
            : ['success' => false, 'message' => 'Transaction PIN update failed.'];
    }

    public function sendVerificationCode($userId, $email, $type)
    {
        
        return $this->verificationService->sendCode($userId, $email, $type);
    }

    public function confirmVerificationCode($email, $code)
    {
        if (!$this->verificationService->verify($email, $code)) {
        return ResponseHelper::error([], 'Invalid or expired code');
    }

    return ResponseHelper::success([], 'Code verified successfully');
    }


    public function updatePasswordAfterVerification($email, $otp, $newPassword)
    {
        $isValid = $this->verificationService->verify($email, $otp);

        if (!$isValid) {
            return [
                'success' => false,
                'message' => 'Invalid or expired code'
            ];
        }

        $user = $this->user->findByEmail($email);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $updated = $this->user->updatePasswordByEmail($user['email'], $hashed);

        return $updated
            ? ['success' => true, 'message' => 'Password updated successfully']
            : ['success' => false, 'message' => 'Failed to update password'];
    }

    public function updatePinAfterVerification($email, $otp, $newPin)
    {
        
        $isValid = $this->verificationService->verify($email, $otp);

        if (!$isValid) {
            return [
                'success' => false,
                'message' => 'Invalid or expired code'
            ];
        }

        $user = $this->user->findByEmail($email);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        $hashed = password_hash($newPin, PASSWORD_DEFAULT);
        $updated = $this->user->updatePinByEmail($user['email'], $hashed);

        return $updated
            ? ['success' => true, 'message' => 'Transaction PIN updated successfully']
            : ['success' => false, 'message' => 'Failed to update PIN'];
    }

    public function setupTransactionPin($email, $pin)
    {
        $user = $this->user->findByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
        $updated = $this->user->updatePinByEmail($user['email'], $hashedPin);

        return $updated
            ? ['success' => true, 'message' => 'Transaction PIN set successfully.']
            : ['success' => false, 'message' => 'Failed to set transaction PIN.'];
    }



}