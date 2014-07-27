<?php


$attributes = array();
$hashes     = array();

if (isset($_POST['message']))
{
    $key                        = 'Message';
    $value                      = $_POST['message'];
    $attributes[$key]           = $value;
    $hash                       = sha1("{$key}: {$value}");
    $hashes[$key]               = $hash;

    $key                        = 'Page URL';
    $value                      = $_POST['pageURL'];
    $attributes[$key]           = $value;
    $hash                       = sha1("{$key}: {$value}");
    $hashes[$key]               = $hash;

    $key                        = 'Script URL';
    $value                      = $_POST['scriptURL'];
    $attributes[$key]           = $value;
    $hash                       = sha1("{$key}: {$value}");
    $hashes[$key]               = $hash;

    $key                        = 'Line Number';
    $value                      = $_POST['lineNumber'];
    $attributes[$key]           = $value;
    $hash                       = sha1("{$key}: {$value}");
    $hashes[$key]               = $hash;
}
else
{
    $key                        = 'Message';
    $value                      = 'Colby: The standard error parameters were not specified.';
    $attributes[$key]           = $value;
    $hash                       = sha1("{$key}: {$value}");
    $hashes[$key]               = $hash;
}

$key                        = 'User Agent';
$value                      = $_SERVER['HTTP_USER_AGENT'];
$attributes[$key]           = $value;
$hash                       = sha1("{$key}: {$value}");
$hashes[$key]               = $hash;

$key                        = 'IP Address';
$value                      = $_SERVER['REMOTE_ADDR'];
$attributes[$key]           = $value;
$hash                       = sha1("{$key}: {$value}");
$hashes[$key]               = $hash;

if (containsExcludedHash($hashes))
{
    exit;
}

if (class_exists('Swift_SmtpTransport'))
{
    $CSS = <<<EOT

        <style>

            th, td
            {
                padding:        5px;
                vertical-align: top;
            }

            td small
            {
                color:          #808080;
                display:        inline-block;
                font-family:    "Courier New";
                padding-top:    5px;
            }

        </style>
EOT;

    $html = "<table>{$CSS}";

    foreach ($attributes as $key => $value)
    {
        $keyHTML    = ColbyConvert::textToHTML($key);
        $valueHTML  = ColbyConvert::textToHTML($value);
        $valueHTML  = "<div>$valueHTML</div>";

        if (isset($hashes[$key]))
        {
            $hashHTML   = ColbyConvert::textToHTML($hashes[$key]);
            $valueHTML  = "{$valueHTML}<div><small>{$hashHTML}</small></div>";
        }

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


/**
 * @Return Boolean
 */
function containsExcludedHash($hashes)
{
    $excludesFilename = Colby::findFile('setup/excludedJavaScriptErrors.txt');

    if ($excludesFilename)
    {
        $excludedHashes = array();
        $lines          = file($excludesFilename);

        foreach ($lines as $line)
        {
            if (preg_match('/^([a-f0-9]{40})/', $line, $matches))
            {
                $excludedHashes[] = $matches[1];
            }
        }

        foreach ($hashes as $hash)
        {
            if (in_array($hash, $excludedHashes))
            {
                return true;
            }
        }
    }

    return false;
}
