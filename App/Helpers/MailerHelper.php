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
        $mail->Password   = 'cekfhutrgmcacyre'; // Gmail App Password
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

    public static function sendOtp($toEmail, $otp)
    {
        return self::sendEmail(
            $toEmail,
            'OTP for Password Reset',
            "Hello,<br><br>
            Your OTP for password reset is <b>$otp</b>.<br>
            It expires in 10 minutes.<br><br>
            If you didn’t request this, please ignore this email.<br><br>
            Regards,<br>Vastrust Support Team"
        );
    }

    public static function sendPinReset($toEmail, $otp)
    {
        return self::sendEmail(
            $toEmail,
            'OTP for PIN Reset',
            "Hello,<br><br>
            Your OTP for your PIN reset is <b>$otp</b>.<br>
            It expires in 10 minutes.<br><br>
            If you didn't request this, please ignore this email.<br><br>
            Regards,<br>Vastrust Support Team"
        );
    }

    public static function sendVerification($toEmail, $code)  //For registration verification
    {
        return self::sendEmail(
            $toEmail,
            'Verify Your Email',
            "Welcome to Vastrust!<br><br>
            Please verify your email with the following code:<br>
            <h2>$code</h2><br>
            This code will expire in 10 minutes.<br><br>
            Regards,<br>Vastrust Support Team"
        );
    }

    public static function sendLoginNotification($email, $firstName)
    {
        $loginTime = date('d F, Y h:i:s A'); // Example: 18 July, 2025 02:45:11 PM
        $currentYear = date('Y');

        $subject = 'Vastrust App Login Notification';

        $body = "
            Dear $firstName,<br><br>
            You have successfully logged into your Vastrust account on: <b>$loginTime</b><br><br>

            If you did not initiate this login, please contact our support immediately at <a href='mailto:info@vastrust.com'>info@vastrust.com</a> or call 0800-1234-567.<br><br>

            For more information, visit our website.<br><br>

            &copy; $currentYear Vastrust Bank. All rights reserved.
        ";

        return self::sendEmail($email, $subject, $body);
    }

    public static function sendTransferNotification($email, $firstName, $amount, $recipient, $balance)
    {
        $subject = 'Vastrust Transaction Alert - Transfer [Debit]';
        $body = "Hello $firstName,<br><br>
            You transferred <b>₦$amount</b> to <b>$recipient</b>.<br>
            Remaining balance: <b>₦$balance</b>.<br><br>
            If this wasn't you, contact us immediately at <a href='mailto:info@vastrust.com'>info@vastrust.com</a> or call 0800-1234-567.<br><br>
            Regards,<br>Vastrust Team";

        return self::sendEmail($email, $subject, $body);
    }

    public static function sendWithdrawalNotification($email, $firstName, $amount, $balance)
    {
        $subject = 'Vastrust Withdrawal Alert';
        $body = "Hello $firstName,<br><br>
            You withdrew <b>₦$amount</b> from your account.<br>
            Available Balance: <b>₦$balance</b>.<br><br>
            If this wasn't you, contact us immediately.<br><br>
            Regards,<br>Vastrust Team";

        return self::sendEmail($email, $subject, $body);
    }

    public static function sendDepositNotification($email, $firstName, $amount, $balance)
    {
        $subject = 'Vastrust - Deposit Confirmation';
        $body = "Hello $firstName,<br><br>
            You deposited <b>₦$amount</b> into your account.<br>
            New Balance: <b>₦$balance</b>.<br><br>
            Thanks for banking with us.<br><br>
            Regards,<br>Vastrust Team";

        return self::sendEmail($email, $subject, $body);
    }

    public static function sendCreditAlertNotification($email, $firstName, $amount, $senderName, $balance)
    {
        $subject = 'Vastrust Transaction Alert - Transfer [Credit]';
        $body = "Hello $firstName,<br><br>
            You received <b>₦$amount</b> from <b>$senderName</b>.<br>
            New Balance: <b>₦$balance</b>.<br><br>
            Thanks for banking with us.<br><br>
            Regards,<br>Vastrust Team";

        return self::sendEmail($email, $subject, $body);
    }
}
