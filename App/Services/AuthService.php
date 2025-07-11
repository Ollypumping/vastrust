<?php
namespace App\Services;

use App\Core\Model;
use App\Services\AccountService;
use App\Models\User;

class AuthService
{
    private $user;
    private $accountService;

    public function __construct()
    {
        $this->user = new User();
        $this->accountService = new AccountService();
    }

    public function register($data, $file)
    {
        if ($this->user->findByEmail($data['email'])) {
            return [
                'success' => false,
                'message' => 'A user with this email already exists.'
            ];
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Handle optional transaction PIN
        $data['transaction_pin'] = !empty($data['transaction_pin'])
            ? password_hash($data['transaction_pin'], PASSWORD_DEFAULT)
            : null;

        // Handle optional occupation
        $data['occupation'] = $data['occupation'] ?? null;

        // Handle passport photo upload
        $photoName = null;
        if (!empty($file['passport_photo']['name'])) {
            $photoName = time() . '_' . $file['passport_photo']['name'];
            $targetPath = __DIR__ . '/../../storage/uploads/' . $photoName;
            move_uploaded_file($file['passport_photo']['tmp_name'], $targetPath);
        }

        $data['passport_photo'] = $photoName;

        // Create user
        $userCreated = $this->user->create([
            'email' => $data['email'],
            'password' => $data['password'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'account_number' => $data['account_number'] ?? null,
            'passport_photo' => $data['passport_photo'],
            'age' => $data['age'],
            'occupation' => $data['occupation'],
            'address' => $data['address'],
            'phone_number' => $data['phone_number'],
            'bvn' => $data['bvn'],
            'transaction_pin' => $data['transaction_pin']
        ]);

        if (!$userCreated) {
            return [
                'success' => false,
                'message' => 'User registration failed.'
            ];
        }

        // Create account
        $userId = $this->user->getLastInsertId();
        $account = $this->accountService->create($userId, $data['account_type'] ?? 'savings');

        if ($account['success']) {
            $this->user->updateAccountNumber($userId, $account['data']['account_number']);
        }

        return [
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
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
        $user = $this->user->findById($userId);

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
            return ['success' => false, 'message' => 'Email not found.'];
        }

        // Mock reset: Assign temporary password
        $tempPassword = 'Temp1234';
        $hashed = password_hash($tempPassword, PASSWORD_DEFAULT);
        $this->user->updatePassword($user['id'], $hashed);

        return [
            'success' => true,
            'message' => "Temporary password assigned (mock): {$tempPassword}"
        ];
    }
}