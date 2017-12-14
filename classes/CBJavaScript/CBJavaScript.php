<?php

class CBJavaScript {

    /**
     * @return null
     */
    static function CBAjax_reportError(stdClass $args) {
        $errorModel = CBModel::valueAsObject($args, 'errorModel');
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

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => CBMessageMarkup::stringToMarkup($firstLine) . "\n\n" . implode("\n\n", $messages),
            'severity' => 3,
        ]);
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
