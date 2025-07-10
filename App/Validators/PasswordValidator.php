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

        return $errors;
    }
}