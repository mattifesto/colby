<?php

final class SCStripe {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v78.js', scliburl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $stripePreferencesModel = CBModelCache::fetchModelByID(
            SCStripePreferences::ID()
        );

        $paymentsEnabled = CBModel::valueToBool(
            $stripePreferencesModel,
            'paymentsEnabled'
        );

        $livePublishableKey = CBModel::valueToString(
            $stripePreferencesModel,
            'livePublishableKey'
        );

        $testPublishableKey = CBModel::valueToString(
            $stripePreferencesModel,
            'testPublishableKey'
        );

        return [
            [
                'SCStripe_apiVersion',
                SCStripe::apiVersion(),
            ],
            [
                'SCStripe_paymentsEnabled',
                $paymentsEnabled,
            ],
            [
                'SCStripe_livePublishableKey',
                $livePublishableKey,
            ],
            [
                'SCStripe_testPublishableKey',
                $testPublishableKey,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- functions -- -- -- -- -- */

    /**
     * @return string
     */
    static function apiVersion(): string {
        return '2018-05-21';
    }


    /**
     * @param object $args
     *
     *      {
     *          apiURL: string
     *          apiKey: string
     *          apiArgs: object
     *      }
     *
     * @return object
     */
    static function call(stdClass $args): stdClass {
        $URL = CBModel::valueToString($args, 'apiURL');
        $key = CBModel::valueToString($args, 'apiKey');
        $apiVersion = SCStripe::apiVersion();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$key}:");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
                "Stripe-Version: {$apiVersion}",
            ]
        );

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query(
                CBModel::valueToObject($args, 'apiArgs')
            )
        );

        if (false) { /* enable to help debug cURL issues */
            $fp = fopen(cbsitedir() . '/php_curl_errors.txt', 'w');
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }

        $resultAsJSON = curl_exec($ch);

        if ($resultAsJSON === false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);

            throw new RuntimeException(
                "cURL error number: {$errno}. cURL error: {$error}"
            );
        }

        curl_close($ch);

        return json_decode($resultAsJSON);
    }
    /* call() */
}
