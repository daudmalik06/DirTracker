<?php
/**
 * Created by PhpStorm.
 * User: dawood.ikhlaq
 * Date: 11/04/2019
 * Time: 12:36
 */

namespace DirTrack;


use PHPMailer\PHPMailer\PHPMailer;

class DirTrack
{
    private $mailer;
    private $directoryToTrack;

    /**
     * DirTrack constructor.
     * @param $directoryToTrack
     */
    public function __construct($directoryToTrack)
    {
        $this->mailer = new PHPMailer;
        $this->directoryToTrack = $directoryToTrack;
    }

    /**
     *
     */
    public function work()
    {
        $timeStampFile = dirname(__DIR__).'/last_timestamp';
        if(!file_exists($timeStampFile))
        {
            file_put_contents($timeStampFile, "");
        }
        $lastTrackedTimeStamp  = (int)file_get_contents($timeStampFile);
        file_put_contents($timeStampFile, time());
        $files = glob_recursive($this->directoryToTrack.'/*');
        $files = array_filter($files, function ($file)use($lastTrackedTimeStamp) {
            return filemtime($file) > $lastTrackedTimeStamp;
        });
        if (!count($files))
        {
            return;
        }
        $files = array_filter($files, 'is_file');
        print count($files).": New File(s) Found".PHP_EOL;
        foreach ($files as $file)
        {
            $this->sendEmailToAdmin($file);
        }
    }

    /**
     * @param $file
     */
    private function sendEmailToAdmin($file)
    {
        if(!env('SEND_MAIL'))
        {
            return ;
        }
        $content = file_get_contents($file);

        //Create a new PHPMailer instance
        $mail = $this->mailer;
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $debug = env('DEBUG') === true ? 2 : 0;
        $mail->SMTPDebug = $debug;

        //Set the hostname of the mail server
        $mail->Host = env('SMTP','smtp.gmail.com');
        $mail->Port = env('SMTP_PORT',587);
        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure =  env('SMTP_ENCRYPTION','tls');

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_USERNAME');
        $mail->Password = env('SMTP_PASSWORD');

        //Set who the message is to be sent from
        $mail->setFrom('no-reply@dirtracker.com', 'Dir Tracker');
        $mail->addReplyTo('no-reply@dirtracker.com', 'Dir Tracker');

        //Set who the message is to be sent to
        $mail->addAddress(env('ADMIN_EMAIL'));
        $mail->Subject = 'New File Has Been Detected';

        $mail->Body = "File: {$file} \nContent Of File:".PHP_EOL.trim($content);
        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo.PHP_EOL;
        } else {
            echo "Message sent!".PHP_EOL;
        }
    }
}