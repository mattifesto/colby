<?php

final class CBSlack {

    /**
     * @param object $args
     *
     *      {
     *          message: string
     *      }
     *
     * @return null
     */
    static function sendMessage($args) {
        $URL = CBModel::value(CBSitePreferences::model(), 'slackWebhookURL');

        if (empty($URL)) {
            return;
        }

        $username = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : CBSitePreferences::siteName();
        $message = CBModel::value($args, 'message', '', function ($value) {
            $message = strval($value);

            if (($length = mb_strlen($message)) > 2000) {
                return trim(mb_substr($message, 0, 1000)) .
                    "\n\n> The original message was {$length} characters long and the middle characters were removed.\n\n" .
                    trim(mb_substr($message, -1000, 1000));
            } else {
                return $message;
            }
        });
        $payload = (object)[
            'channel' => '#errors',
            'username' => $username,
            'text' =>  $message,
        ];

        $payload->attachments = CBModel::valueToArray($args, 'attachments');

        /**
         * It's very important that the $data passed as the CURLOPT_POSTFIELDS
         * be specified as an array. When passed as an array to value is
         * properly encoded by curl. Many examples on the internet will pass
         * this value as key=value string which only works when the value
         * contains no special characters.
         */

        $data = ['payload' => json_encode($payload)];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if ($result === false) {
            throw new RuntimeException('Curl error: ' . curl_error($ch));
        } else if (($code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE)) != 200) {
            throw new RuntimeException("Slack error: {$code} {$result}");
        }

        curl_close($ch);
    }
}
