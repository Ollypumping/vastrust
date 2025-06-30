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
}