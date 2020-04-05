<?php
namespace Pickle\Engine;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once ROOT.'/vendor/autoload.php';
require_once ROOT . '/core/App/Interfaces/EmailInterface.php';

class Email implements \EmailInterface{

    static $name = "Coursonline";
    static $email = "noreply@iconia.dev";

    /*static $host = 'smtp.mailgun.org';
    static $username = 'postmaster@wallp.fr';
    static $pass = '907e03d8e615f1946f12b1bebfe7deec-41a2adb4-3b877b42';
    static $port = 587;*/

    static $host = 'smtp.mailgun.org';
    static $username = 'postmaster@iconia.dev';
    static $pass = 'F%a7q2h7';
    static $port = 587;

    static function send($subject, $data, $name, $mailto = null, $from = null){
        return false;
        if($mailto == null){
            return false;
        }
        $mail = new PHPMailer(true);
        //$message = utf8_decode($message);
        $subject = utf8_decode($subject);
        if ($from == null) {
            $from = self::$email;
        }
        $header = self::generate_header($from);

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = 2;
            $mail->Host = self::$host;           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, ssl also accepted
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = self::$username;                 // SMTP username
            $mail->Password = self::$pass;           // SMTP password
            $mail->Port = self::$port;
    //Recipients
            $mail->setFrom(self::$email, self::$name);
            $mail->addAddress($mailto);              // Name is optional

    //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = self::generate_content($data, $name);
            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

        return $mail;
    }

    static function generate_header($from){
        
        date_default_timezone_set('UTC');

        $header = 'MIME-Version: 1.0'.PHP_EOL
        .'From: '.self::$name.'<'.$from.'>'.PHP_EOL
        .'Return-Path: '.$from.PHP_EOL
        .'Reply-To: '.$from.PHP_EOL
        .'Organization: '.self::$name.PHP_EOL 
        .'X-Priority: 3 (Normal)'.PHP_EOL 
        .'Content-Type: text/html; charset="iso-8859-1"'.PHP_EOL
        .'Content-Transfer-Encoding: 8bit'.PHP_EOL
        .'X-Mailer: PHP '.PHP_EOL
        .'Date:'. date("r") . PHP_EOL;

        return $header;

    }

    static function generate_content($data, $template_name){
        if($data != null){
            extract($data);
        }
        ob_start();
        $path = ROOT."/core/App/email-templates/html/$template_name.php";
        require($path);
        return ob_get_clean();
    }

}


?>