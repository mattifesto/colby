<?php

final class CBUICaptcha {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [
            'https://www.google.com/recaptcha/api.js',
            Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__),
        ];
    }

    /**
     * @return [[string, mixed]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBUICaptchaReCAPTCHASiteKey', CBSitePreferences::reCAPTCHASiteKey()],
        ];
    }

    /**
     * @return bool
     */
    static function responseKeyIsValid($captchaResponseKey) {
        $secretKey = CBSitePreferences::reCAPTCHASecretKey();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'response' => $captchaResponseKey,
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

            throw new RuntimeException("cURL error number: {$errno}. cURL error: {$error}");
        }

        curl_close($ch);

        $result = json_decode($result);
        return $result->success;
    }
}
