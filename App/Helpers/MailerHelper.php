<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerHelper
{
    private static function getMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'olayemiojo49@gmail.com';
        $mail->Password   = 'wgbtwnmjnqonnvak'; // App Password from Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('olayemiojo49@gmail.com', 'Vastrust Support');
        $mail->isHTML(true);

        return $mail;
    }

    public static function sendEmail($toEmail, $subject, $body): bool|string
    {
        try {
            $mail = self::getMailer();
            $mail->addAddress($toEmail);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }

    public static function sendOtp($toEmail, $otp): bool|string
    {
        $subject = 'OTP for Password Reset';
        $body = "
            Hello,<br><br>
            Your OTP for password reset is <b>$otp</b>.<br>
            It expires in 10 minutes.<br><br>
            If you didn’t request this, please ignore this email.<br><br>
            Regards,<br>Vastrust Support Team
        ";

        return self::sendEmail($toEmail, $subject, $body);
    }

    public static function sendPinReset($toEmail, $otp): bool|string
    {
        $subject = 'OTP for Pin Reset';
        $body = "
            Hello,<br><br>
            Your OTP for your pin reset is <b>$otp</b>.<br>
            It expires in 10 minutes.<br><br>
            If you didn't request this, please ignore this email.<br><br>
            Regards,<br>Vastrust Support Team
        ";

        return self::sendEmail($toEmail, $subject, $body);
    }

    public static function sendVerification($toEmail, $code): bool|string
    {
        $subject = 'Verify Your Email';
        $body = "
            Welcome to Vastrust!<br><br>
            Please verify your email with the following code:<br>
            <h2>$code</h2><br>
            This code will expire in 10 minutes.<br><br>
            Regards,<br>Vastrust Support Team
        ";

        return self::sendEmail($toEmail, $subject, $body);
    }
}
