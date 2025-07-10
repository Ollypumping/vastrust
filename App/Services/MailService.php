<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public function sendResetCode($to, $code)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // or smtp.gmail.com
            $mail->SMTPAuth   = true;
            $mail->Username   = 'olayemiojo49@gmail.com';
            $mail->Password   = 'ctuwysjlidjewtnw';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('olayemiojo49@gmail.com', 'Vastrust Bank');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset Code';
            $mail->Body    = "Your reset code is <strong>{$code}</strong>. It expires in 10 minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
