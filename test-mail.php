<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'olayemiojo49@gmail.com';
    $mail->Password   = 'wgbtwnmjnqonnvak';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('your_email@gmail.com', 'Test Mail');
    $mail->addAddress('olayemiojo49@gmail.com'); // Try same as sender first

    $mail->isHTML(true);
    $mail->Subject = 'Testing OTP Email';
    $mail->Body    = 'This is a test email from PHPMailer.';

    $mail->send();
    echo 'Test mail sent';
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}