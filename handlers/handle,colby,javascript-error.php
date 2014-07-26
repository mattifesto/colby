<?php

$attributes = array();

if (isset($_POST['message']))
{
    $attributes['Message']      = $_POST['message'];
    $attributes['Page URL']     = $_POST['pageURL'];
    $attributes['Script URL']   = $_POST['scriptURL'];
    $attributes['Line Number']  = $_POST['lineNumber'];
}

$attributes['User Agent']   = $_SERVER['HTTP_USER_AGENT'];
$attributes['IP Address']   = $_SERVER['REMOTE_ADDR'];

if (class_exists('Swift_SmtpTransport'))
{
    $html = '<table>';

    foreach ($attributes as $key => $value)
    {
        $keyHTML    = ColbyConvert::textToHTML($key);
        $valueHTML  = ColbyConvert::textToHTML($value);

        $html .= "<tr><th style='text-align: right;'>{$keyHTML}</th><td>{$valueHTML}</td></tr>";
    }

    $html .= '</table>';

    $transport = Swift_SmtpTransport::newInstance(COLBY_EMAIL_SMTP_SERVER,
                                                  COLBY_EMAIL_SMTP_PORT,
                                                  COLBY_EMAIL_SMTP_SECURITY);

    $transport->setUsername(COLBY_EMAIL_SMTP_USER);
    $transport->setPassword(COLBY_EMAIL_SMTP_PASSWORD);

    $mailer = Swift_Mailer::newInstance($transport);

    $messageSubject = COLBY_SITE_NAME . ' JavaScript Error (' . time() . ')';
    $messageFrom = array(COLBY_EMAIL_SENDER => COLBY_EMAIL_SENDER_NAME);
    $messageTo = array(COLBY_SITE_ADMINISTRATOR);

    $message = Swift_Message::newInstance();
    $message->setSubject($messageSubject);
    $message->setFrom($messageFrom);
    $message->setTo($messageTo);
    $message->setBody($html, 'text/html');

    $mailer->send($message);
}

error_log('JavaScript error: ' . var_export($attributes, true));
