<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function send($toEmail, $toName, $subject, $body)       //Mailer::send
    {
        if (SMTP_USER === '' || SMTP_PASS === '') {
            return false;
        }

        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;   
        $mail->Port       = SMTP_PORT;  
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'tls';
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;     
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, SITE_NAME);
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();
    }
}