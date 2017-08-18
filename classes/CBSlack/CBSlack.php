<?php

final class CBSlack {

    /**
     * @param string $args->message
     *
     * @return null
     */
    static function sendMessage($args) {
        $URL = CBModel::value(CBSitePreferences::model(), 'slackWebhookURL');

        if (empty($URL)) {
            return;
        }

        $message = CBModel::value($args, 'message', '', 'strval');
        $payload = (object)[
            'channel' =>  '#errors',
            'username' =>  CBSitePreferences::siteName(),
            'text' =>  $message,
        ];

        $payload->attachments = CBModel::valueAsObjects($args, 'attachments');

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
            CBLog::addMessage(__METHOD__, 3, 'Curl error: ' . curl_error($ch));
        } else if (($code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE)) != 200) {
            CBLog::addMessage(__METHOD__, 3, "Slack error: {$code} {$result}");
        }

        curl_close($ch);
    }
}
