<?php
namespace App\Services;

use App\Models\Verification;
use App\Helpers\MailerHelper;
use App\Helpers\ResponseHelper;

class VerificationService
{
    public function sendCode($userId, $email, $type)
    {
        $otp = rand(100000, 999999);
        $verification = new Verification();
        $verification->invalidateCode($email);
        $verification->saveCode($userId, $email, $otp);

        $sent = false;

        switch ($type) {
            case 'register':
                $sent = MailerHelper::sendVerification($email, $otp);
                break;
            case 'reset_password':
                $sent = MailerHelper::sendOtp($email, $otp);
                break;
            case 'reset_pin':
                $sent = MailerHelper::sendPinReset($email, $otp);
                break;
        }

        if ($sent) {
            return [
                'success' => true,
                'message' => 'Verification code sent successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to send verification code'
            ];
        }
    }

    public function verify($email, $otp)
    {
        $verification = new Verification();
        $valid = $verification->verifyCode($email, $otp);

        if ($valid) {
            $verification->invalidateCode($email);
            return [
                'success' => true,
                'message' => 'Verification successful'
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid or expired verification code'
        ];
    }
}
