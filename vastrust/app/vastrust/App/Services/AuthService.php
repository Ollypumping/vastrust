<?php
namespace App\Services;

use App\Models\User;

class AuthService
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
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

        $uploadPath = 'storage/uploads/';
        $filename = uniqid('passport_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $destination = $uploadPath . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'message' => 'Failed to upload passport photo.'
            ];
        }

        $data['passport_photo'] = $destination;

        $success = $this->user->create([
            'email' => $data['email'],
            'password' => $data['password'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'passport_photo' => $data['passport_photo'],
            'age' => $data['age'],
            'occupation' => $data['occupation'],
            'address' => $data['address'],
            'phone_number' => $data['phone_number'],
            'bvn' => $data['bvn'],
            'nok_first_name' => $data['nok_first_name'],
            'nok_last_name' => $data['nok_last_name'],
            'nok_phone_number' => $data['nok_phone_number'],
            'nok_address' => $data['nok_address']
        ]);

        return $success
            ? ['success' => true, 'message' => 'User registered successfully.', 'data' => ['email' => $data['email']]]
            : ['success' => false, 'message' => 'User registration failed.'];
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