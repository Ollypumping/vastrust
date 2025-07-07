<?php
namespace App\Validators;

class LoginValidator
{
    public function validate($data)
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        }

        return $errors;
    }
}