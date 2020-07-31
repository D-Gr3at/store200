<?php 
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/phpmailer/phpmailer/src/Exception.php';
    require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'vendor/phpmailer/phpmailer/src/SMTP.php';
    require 'vendor/autoload.php';
    $mail = new PHPMailer(TRUE);


    /* SMTP parameters. */

     /* Tells PHPMailer to use SMTP. */
    $mail->isSMTP();
    
    /* SMTP server address. */
    $mail->Host = 'smtp.gmail.com';

    /* Use SMTP authentication. */
    $mail->SMTPAuth = TRUE;
    
    /* Set the encryption system. */
    $mail->SMTPSecure = 'tls';
    
    /* SMTP authentication username. */
    $mail->Username = 'billeibinabo@gmail.com';
    
    /* SMTP authentication password. */
    $mail->Password = 'majesty08162530944';
    
    /* Set the SMTP port. */
    $mail->Port = 587;

?>