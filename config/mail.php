<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function logMail($message) {
    $logFile = __DIR__ . "/mail_log.txt";
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    // REMOVE ALL DEBUG OUTPUT FROM BROWSER
    $mail->SMTPDebug  = 0; 
    $mail->Debugoutput = function($str, $level) {
        logMail("SMTP: " . $str);
    };

    logMail("Sending mail to: $to | Subject: $subject");

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // Gmail credentials
        $mail->Username   = 'harsharouniyar2003@gmail.com';
        $mail->Password   = 'bgls hbnq pblg cchk';   // your new app password

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('harsharouniyar2003@gmail.com', 'Smart Health');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        logMail("SUCCESS");
        return true;

    } catch (Exception $e) {
        logMail("ERROR: " . $mail->ErrorInfo);
        return false;
    }
}
