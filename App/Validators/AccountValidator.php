<?php
namespace App\Validators;

class AccountValidator
{
    public function validateCreate($data)
    {
        $errors = [];

        if (!empty($data['account_type'])) {
            $allowed = ['savings', 'current'];
            if (!in_array(strtolower($data['account_type']), $allowed)) {
                $errors['account_type'] = 'Invalid account type. Allowed: savings, current.';
            }
        }

        return $errors;
    }
}