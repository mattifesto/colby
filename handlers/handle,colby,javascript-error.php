<?php

$response = new CBAjaxResponse();

$attributes = array();
$hashes = array();

if (isset($_POST['message'])) {
    $message                    = $_POST['message'];

    $key                        = 'Message';
    $value                      = $message;
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

    $key                        = 'Script + Message';
    $value                      = "{$hashes['Script URL']} + {$hashes['Message']}";
    $attributes[$key]           = $value;
    $hash                       = sha1("{$key}: {$value}");
    $hashes[$key]               = $hash;
} else {
    $message                    = 'Unspecified';
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

if (containsExcludedHash($hashes)) {
    exit;
}

$messages = [];

foreach ($attributes as $key => $value) {
    $hash = $hashes[$key];
    $messages[] = "*{$key}*\n{$value}\n$hash\n";
}

CBSlack::sendMessage((object)[
    'message' => "JavaScript: {$message}",
    'attachments' => [
        (object)[
            'text' => implode("\n", $messages),
            'mrkdwn_in' => ['text'],
        ],
    ],
]);

CBLog::addMessage('/javascript-error/', 3, $message, (object)[
    'text' => implode("\n", $messages),
]);

$response->wasSuccessful = true;
$response->send();

/**
 * @Return Boolean
 */
function containsExcludedHash($hashes) {
    $excludesFilename = Colby::findFile('setup/excludedJavaScriptErrors.txt');

    if ($excludesFilename) {
        $excludedHashes = array();
        $lines          = file($excludesFilename);

        foreach ($lines as $line) {
            if (preg_match('/^([a-f0-9]{40})/', $line, $matches)) {
                $excludedHashes[] = $matches[1];
            }
        }

        foreach ($hashes as $hash) {
            if (in_array($hash, $excludedHashes)) {
                return true;
            }
        }
    }

    return false;
}
