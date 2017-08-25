<?php

class CBJavaScript {

    /**
     * @return null
     */
    static function reportErrorAjax(stdClass $args) {
        $response = new CBAjaxResponse();
        $errorModel = CBModel::valueAsObject($args, 'errorModel');
        //$errorModel = (object)[];
        $pMessage = CBModel::value($errorModel, 'message');
        $pPageURL = CBModel::value($errorModel, 'pageURL');
        $pSourceURL = CBModel::value($errorModel, 'sourceURL');
        $pLine = CBModel::value($errorModel, 'line', null, 'intval');
        $pColumn = CBModel::value($errorModel, 'column', null, 'intval');

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

        $messages = [];

        foreach ($attributes as $key => $value) {
            $hash = $hashes[$key];
            $messages[] = "*{$key}*\n{$value}\n$hash\n";
        }

        $message = CBConvert::javaScriptErrorToMessage($errorModel);
        $link = cbsiteurl() . '/admin/page/?class=CBAdminPageForLogs';

        if (CBJavaScript::shouldReportToDeveloper($errorModel, $hashes)) {
            CBSlack::sendMessage((object)[
                'message' => "{$message} <{$link}|link>",
            ]);
        }

        CBLog::addMessage(__METHOD__, 3, $message, (object)[
            'text' => implode("\n", $messages),
        ]);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function reportErrorGroup() {
        return 'Public';
    }

    /**
     * @return bool
     */
    static function shouldReportToDeveloper(stdClass $errorModel, array $hashes) {
        if (empty($errorModel->message) && empty($errorModel->sourceURL) && empty($errorModel->line)) {
            return false;
        }

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
                    return false;
                }
            }
        }

        return true;
    }
}
