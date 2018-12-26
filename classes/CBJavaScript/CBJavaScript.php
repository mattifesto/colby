<?php

class CBJavaScript {

    /**
     * @return null
     */
    static function CBAjax_reportError(stdClass $args) {
        $errorModel = CBModel::valueToObject($args, 'errorModel');
        $pMessage = CBModel::value($errorModel, 'message');
        $pPageURL = CBModel::value($errorModel, 'pageURL');
        $pSourceURL = CBModel::value($errorModel, 'sourceURL');
        $pLine = CBModel::value($errorModel, 'line', null, 'intval');
        $pColumn = CBModel::value($errorModel, 'column', null, 'intval');

        $attributes = array();
        $hashes = array();

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
        $firstLine = 'Error ' . CBConvert::javaScriptErrorToMessage($errorModel);

        foreach ($attributes as $key => $value) {
            $keyAsMarkup = CBMessageMarkup::stringToMarkup($key);
            $valueAsMarkup = CBMessageMarkup::stringToMarkup(strval($value));
            $hashAsMarkup = CBMessageMarkup::stringToMarkup($hashes[$key]);
            $messages[] = "({$keyAsMarkup} (strong))((br)){$valueAsMarkup}((br))$hashAsMarkup";
        }

        $link = cbsiteurl() . '/admin/page/?class=CBLogAdminPage';

        if (CBJavaScript::shouldReportToDeveloper($errorModel, $hashes)) {
            CBSlack::sendMessage((object)[
                'message' => "{$firstLine} <{$link}|link>",
            ]);
        }

        $firstLineAsMessage = CBMessageMarkup::stringToMarkup($firstLine);
        $messagesAsMessage = implode("\n\n", $messages);

        $stack = CBModel::valueToString($errorModel, 'stack');

        if (!empty($stack)) {

            /**
             * @TODO 2018_12_25
             *
             *      Give the JavaScript stack a nicer appearance.
             */
            $stackAsMessage = CBMessageMarkup::stringToMessage($stack);

            $stackAsMessage = <<<EOT

                --- dl
                    --- dt
                    stack
                    ---
                    --- dd
                        --- pre\n{$stackAsMessage}
                        ---
                    ---
                ---

EOT;
        } else {
            $stackAsMessage = '';
        }

        $message = <<<EOT

            {$firstLineAsMessage}

            {$messagesAsMessage}

            {$stackAsMessage}

EOT;

        CBLog::log(
            (object)[
                'message' => $message,
                'severity' => 3,
                'sourceClassName' => __CLASS__,
                'sourceID' => '0d0c0f9b9a21d20421001b7071816f3abc08ae79',
            ]
        );
    }

    /**
     * @return object
     */
    static function CBAjax_reportError_group() {
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
