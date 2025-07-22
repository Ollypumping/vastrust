<?php
namespace App\Validators;

class RegisterValidator
{
    public function validate($data, $file)
    {
        $errors = [];

        // Required text fields
        $requiredFields = [
            'email', 'password', 'first_name', 'last_name', 'dob', //'occupation',
            'address', 'phone_number', 'bvn', //'transaction_pin'
            
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

        // Validate date of birth (dob)
        if (!empty($data['dob'])) {
            $dob = $data['dob'];
            
            // Check if it's a valid date
            $dateParts = explode('-', $dob);
            if (count($dateParts) !== 3 || !checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
                $errors['dob'] = 'Date of birth must be a valid date in YYYY-MM-DD format.';
            } else {
                $birthTimestamp = strtotime($dob);
                $now = time();

                if ($birthTimestamp > $now) {
                    $errors['dob'] = 'Date of birth cannot be in the future.';
                }

                $birthDate = new \DateTime($dob);
                $today = new \DateTime('today');
                $age = $birthDate->diff($today)->y;
                if ($age < 18) {
                    $errors['dob'] = 'You must be at least 18 years old to register.';
                }
            }
        } else {
            $errors['dob'] = 'Date of birth is required.';
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


        

        return $errors;
    }
}