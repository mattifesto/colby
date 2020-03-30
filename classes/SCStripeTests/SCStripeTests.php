<?php

final class SCStripeTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v87.js', scliburl())
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBMessageMarkup',
            'CBModel',
            'SCStripe',
        ];
    }



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'tokens',
            ],
            (object)[
                'name' => 'transaction',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    static function CBTest_transaction(): stdClass {
        $stripePreferencesModel = CBModelCache::fetchModelByID(SCStripePreferences::ID());
        $publishableStripeAPIKey = CBModel::valueToString($stripePreferencesModel, 'testPublishableKey');
        $secretStripeAPIKey = CBModel::valueToString($stripePreferencesModel, 'testSecretKey');

        if (empty($publishableStripeAPIKey) || empty($secretStripeAPIKey)) {
            return (object)[
                'succeeded' => true,
                'message' => 'Tests not run, there are no test API keys set.',
            ];
        }

        $result = SCStripe::call((object)[
            'apiURL' => 'https://api.stripe.com/v1/tokens',
            'apiKey' => $publishableStripeAPIKey,
            'apiArgs' => (object)[
                'card[number]' => '4242 4242 4242 4242',
                'card[exp_year]' => '2020',
                'card[exp_month]' => '12',
                'card[cvc]' => '123',
            ],
        ]);

        $token = CBModel::valueToString($result, 'id');

        if (!preg_match('/^tok_/', $token)) {
            $resultAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($result)
            );

            $message = <<<EOT

                No token was found in the response of the call to
                (https://api.stripe.com/v1/tokens (code)).

                Response:

                --- pre\n{$resultAsMessage}
                ---

EOT;

            return (object)[
                'succeeded' => false,
                'message' => $message,
            ];
        }

        $result = SCStripe::call((object)[
            'apiURL' => 'https://api.stripe.com/v1/charges',
            'apiKey' => $secretStripeAPIKey,
            'apiArgs' => (object)[
                'amount' => '2000',
                'capture' => 'false',
                'currency' => 'usd',
                'source' => $token,
                'description' => __METHOD__,
            ],
        ]);

        $chargeID = CBModel::valueToString($result, 'id');
        $object = CBModel::valueToString($result, 'object');
        $captured = CBModel::valueToBool($result, 'captured');
        $amount = CBModel::valueAsInt($result, 'amount');

        if (
            !preg_match('/^ch_/', $chargeID) ||
            $object !== 'charge' ||
            $captured !== false ||
            $amount !== 2000
        ) {
            $resultAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($result)
            );

            $message = <<<EOT

                One or more of the following tests on the response of the call
                to (https://api.stripe.com/v1/charges (code)) was not true.

                --- ul
                "id" starts with "ch_"

                "object" === "charge"

                "captured" === false

                "amount" === 2000
                ---

                Response:

                --- pre\n{$resultAsMessage}
                ---

EOT;

            return (object)[
                'succeeded' => false,
                'message' => $message,
            ];
        }

        $URL = "https://api.stripe.com/v1/charges/{$chargeID}/capture";
        $result = SCStripe::call((object)[
            'apiURL' => $URL,
            'apiKey' => $secretStripeAPIKey,
            'apiArgs' => (object)[
                'amount' => '1000',
            ],
        ]);

        $chargeID2 = CBModel::valueToString($result, 'id');
        $captured = CBModel::valueToBool($result, 'captured');
        $amountRefunded = CBModel::valueAsInt($result, 'amount_refunded');

        if (
            $chargeID2 !== $chargeID ||
            $captured !== true ||
            $amountRefunded !== 1000
        ) {
            $resultAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($result)
            );

            $message = <<<EOT

                One or more of the following tests on the response of the call
                to ({$URL} (code)) was not true.

                --- ul
                id === {$chargeID}

                captured === true

                amount_refunded === 1000
                ---

                Response:

                --- pre\n{$resultAsMessage}
                ---

EOT;

            return (object)[
                'succeeded' => false,
                'message' => $message,
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }
}
