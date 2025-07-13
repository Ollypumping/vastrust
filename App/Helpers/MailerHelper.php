<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerHelper
{
    public static function sendOtp($toEmail, $otp)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'olayemiojo49@gmail.com';          // your Gmail address
            $mail->Password   = 'wgbtwnmjnqonnvak';                // your Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Sender & Recipient
            $mail->setFrom('olayemiojo49@gmail.com', 'Vastrust Support');
            $mail->addAddress($toEmail);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'OTP for Password Reset';
            $mail->Body    = "
                Hello,<br><br>
                Your OTP for password reset is <b>$otp</b>.<br>
                This code will expire in 10 minutes.<br><br>
                If you didn't request this, please ignore this email.<br><br>
                Regards,<br>
                Vastrust Support Team
            ";

            // Log for debugging
            error_log("Sending OTP to $toEmail with code $otp");

            // Send mail
            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
