<?php

final class CBCurl {

    /* -- functions -- -- -- -- -- */



    /**
     * @param string $originalURL
     *
     * @return string
     */
    static function fetchRedirectURL(
        $originalURL
    ): string {
        $ch = curl_init(
            $originalURL
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_exec($ch);

        $errorNumber = curl_errno($ch);

        if ($errorNumber !== 0) {
            $errorMessage = curl_error($ch);

            throw new Exception(
                "cURL error: {$errorMessage} ({$errorNumber})"
            );
        }

        $httpCode = curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

        if ($httpCode !== 200) {
            throw new Exception(
                "cURL HTTP code: {$httpCode}"
            );
        }

        $effectiveURL = curl_getinfo(
            $ch,
            CURLINFO_EFFECTIVE_URL
        );

        curl_close($ch);

        return $effectiveURL;
    }
    /* fetchRedirectURL() */

}
