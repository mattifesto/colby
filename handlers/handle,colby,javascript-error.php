<?php

$response = new CBAjaxResponse();

/* The error contains all of the information we need. Once it is confirmed to be
   relatively pervasive, drop the other data sources. */

$pError = cb_post_value('errorAsJSON', (object)[], 'json_decode');
$pMessage = cb_post_value('message', '');
$pPageURL = cb_post_value('pageURL', '');
$pSourceURL = cb_post_value('sourceURL', '');
$pLine = cb_post_value('line', null, 'intval');
$pColumn = cb_post_value('column', null, 'intval');

if (empty($pError->message)) { $pError->message = $pMessage; }
if (empty($pError->sourceURL)) { $pError->sourceURL = $pSourceURL; }
if (empty($pError->line)) { $pError->line = $pLine; }
if (empty($pError->column)) { $pError->column = $pColumn; }

$attributes = array();
$hashes = array();

if (isset($_POST['message'])) {
    $key                        = 'Message';
    $attributes[$key]           = $pMessage;
    $hash                       = sha1("{$key}: {$pMessage}");
    $hashes[$key]               = $hash;

    $key                        = 'Page URL';
    $attributes[$key]           = $pPageURL;
    $hash                       = sha1("{$key}: {$pPageURL}");
    $hashes[$key]               = $hash;

    $key                        = 'Script URL';
    $attributes[$key]           = $pSourceURL;
    $hash                       = sha1("{$key}: {$pSourceURL}");
    $hashes[$key]               = $hash;

    $key                        = 'Line Number';
    $attributes[$key]           = $pLine;
    $hash                       = sha1("{$key}: {$pLine}");
    $hashes[$key]               = $hash;

    $key                        = 'Script + Message';
    $value                      = "{$hashes['Script URL']} + {$hashes['Message']}";
    $attributes[$key]           = $value;
    $hash                       = sha1("{$key}: {$value}");
    $hashes[$key]               = $hash;
} else {
    $key                        = 'Message';
    $pMessage                   = 'Colby: The standard error parameters were not specified.';
    $attributes[$key]           = $pMessage;
    $hash                       = sha1("{$key}: {$pMessage}");
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

$message = CBConvert::javaScriptErrorToMessage($pError);
$link = cbsiteurl() . '/admin/page/?class=CBAdminPageForLogs';

CBSlack::sendMessage((object)[
    'message' => "{$message} <{$link}|link>",
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
