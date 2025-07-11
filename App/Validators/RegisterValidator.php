<?php
namespace App\Validators;

class RegisterValidator
{
    public function validate($data, $file)
    {
        $errors = [];

        // Required text fields
        $requiredFields = [
            'email', 'password', 'first_name', 'last_name', 'age',
            'address', 'phone_number', 'bvn'
            
        ];

        foreach ($requiredFields as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        // Email format
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email format is invalid.';
        }

        // BVN should be 11 digits
        if (!empty($data['bvn']) && !preg_match('/^\d{11}$/', $data['bvn'])) {
            $errors['bvn'] = 'BVN must be exactly 11 digits.';
        }

        // Age must be a positive integer
        if (!empty($data['age']) && (!is_numeric($data['age']) || $data['age'] < 1)) {
            $errors['age'] = 'Age must be a valid number.';
        }

        // Phone numbers should be digits (optional length check)
        $phoneFields = ['phone_number'];
        foreach ($phoneFields as $phoneField) {
            if (!empty($data[$phoneField]) && !preg_match('/^0\d{10}$/', $data[$phoneField])) {
                $errors[$phoneField] = ucfirst(str_replace('_', ' ', $phoneField)) . ' must be an 11-digit number starting with 0.';
            }
        }

        // Passport photo checks
        // if (!empty($files['passport_photo']['name'])) {
        //     $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        //     if (!in_array($files['passport_photo']['type'], $allowed)) {
        //         $errors['passport_photo'] = 'Invalid image type.';
        //     }
        // }
        // Pin checks
        if (!empty($data['transaction_pin']) && !preg_match('/^\d{4}$/', $data['transaction_pin'])) {
            $errors['transaction_pin'] = 'Transaction PIN must be a 4-digit number.';
        }
        return $errors;
    }
}