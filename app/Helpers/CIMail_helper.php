<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/** Send Email Function Using PHPMailer Library */
if(!function_exists('sendEmail')) {
    function sendEmail($mailConfig) {
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';

        //Server settings
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;                           //Enable verbose debug output
        $mail->isSMTP();                                //Send using SMTP
        $mail->Host       = env('EMAIL_HOST');          //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                       //Enable SMTP authentication
        $mail->Username   = env('EMAIL_USERNAME');      //SMTP username
        $mail->Password   = env('EMAIL_PASSWORD');      //SMTP password
        $mail->SMTPSecure = env('EMAIL_ENCRYPTION');    //Enable implicit TLS encryption
        $mail->Port       = env('EMAIL_PORT');          //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->setFrom($mailConfig['mail_from_email'],$mailConfig['mail_from_name']);
        $mail->addAddress($mailConfig['mail_recipient_email'],$mailConfig['mail_recipient_name']);
        $mail->isHTML(true);
        $mail->Subject = $mailConfig['mail_subject'];
        $mail->Body = $mailConfig['mail_body'];
        if( $mail->send() ) {
            return true;
        }else {
            return false;
        }
    }
}