<?php

final class CBUICaptcha {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            'https://www.google.com/recaptcha/api.js',
            Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__),
        ];
    }

    /**
     * @return [[string, mixed]]
     */
    public static function requiredJavaScriptVariables() {
        return [
            ['CBUICaptchaReCAPTCHASiteKey', CBSitePreferences::reCAPTCHASiteKey()],
        ];
    }

    /**
     * @return null
     */
    public static function verifyForAjax() {
        $response = new CBAjaxResponse();
        $responseKey = $_POST['responseKey'];
        $secretKey = CBSitePreferences::reCAPTCHASecretKey();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'response' => $responseKey,
            'secret' => $secretKey,
        ]);

        if (false) { /* enable to help debug cURL issues */
            $fp = fopen(CBSiteDirectory . '/php_curl_errors.txt', 'w');
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }

        $result = curl_exec($ch);

        if ($result === false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);

            $response->wasSuccessful = false;
            $response->message = "cURL error number: {$errno}. cURL error: {$error}";
        } else {
            $result = json_decode($result);
            $response->wasSuccessful = $result->success;
        }

        curl_close($ch);

        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function verifyForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }
}
