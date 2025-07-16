<?php
namespace App\Validators;

class PasswordValidator
{
    public function validateChange($data)
    {
        $errors = [];

        if (empty($data['old_password'])) {
            $errors['old_password'] = 'Old password is required.';
        }

        if (empty($data['new_password'])) {
            $errors['new_password'] = 'New password is required.';
        } elseif (strlen($data['new_password']) < 6) {
            $errors['new_password'] = 'New password must be at least 6 characters.';
        }

        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Confirm password is required.';
        } elseif ($data['new_password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'New password and confirm password do not match.';
        }

        return $errors;
    }

    public function validatePinChange($data)
    {
        $errors = [];

        if (empty($data['old_pin'])) {
            $errors['old_pin'] = 'Old PIN is required.';
        }

        if (empty($data['new_pin'])) {
            $errors['new_pin'] = 'New PIN is required.';
        
        } elseif (!preg_match('/^\d{4}$/', $data['new_pin'])) {
            $errors['new_pin'] = 'New PIN must be a 4-digit number.';
        }
        if (empty($data['confirm_pin'])) {
            $errors['confirm_pin'] = 'Confirm PIN is required.';
        } elseif ($data['new_pin'] !== $data['confirm_pin']) {
            $errors['confirm_pin'] = 'New PIN and Confirm PIN do not match.';
        }

        return $errors;
    }
}