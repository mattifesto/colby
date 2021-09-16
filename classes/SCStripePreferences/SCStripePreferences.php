<?php

final class
SCStripePreferences {

    /**
     * This variable will be set to a substitute ID to be used by
     * SCStripePreferences while tests are running.
     */
    static $testID = null;


    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'className' => 'SCStripePreferences',
                'ID' => SCStripePreferences::ID(),
            ]
        );

        CBModelUpdater::save($updater);
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBModelUpdater',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): ?stdClass {
        $stripePreferencesModel = (object)[
            'liveSecretKey' => SCStripePreferences::valueAsStripeAPIKey(
                $spec,
                'liveSecretKey'
            ),

            'testPublishableKey' => SCStripePreferences::valueAsStripeAPIKey(
                $spec,
                'testPublishableKey',
                /* testonly: */ true
            ),
            'testSecretKey' => SCStripePreferences::valueAsStripeAPIKey(
                $spec,
                'testSecretKey',
                /* testonly: */ true
            ),

            'paymentsEnabled' => CBModel::valueToBool($spec, 'paymentsEnabled'),
        ];

        SCStripePreferences::setLivePublishableKey(
            $stripePreferencesModel,
            SCStripePreferences::getLivePublishableKey(
                $spec
            )
        );

        return $stripePreferencesModel;
    }
    /* CBModel_build() */



    /* -- accessors -- */



    /**
     * @param object stripePreferencesModel
     *
     * @return string|null
     */
    static function
    getLivePublishableKey(
        stdClass $stripePreferencesModel
    ): ?string {
        return SCStripePreferences::valueAsStripeAPIKey(
            $stripePreferencesModel,
            'livePublishableKey'
        );
    }
    /* getLivePublishableKey() */



    /**
     * @param object $stripePreferencesModel
     * @param string|null $livePublishableKey
     *
     * @return void
     */
    static function
    setLivePublishableKey(
        stdClass $stripePreferencesModel,
        ?string $livePublishableKey
    ): void {
        $stripePreferencesModel->livePublishableKey = $livePublishableKey;
    }
    /* setLivePublishableKey() */



    /* -- functions -- */



    /**
     * @return ID
     */
    static function
    ID(
    ): string {
        return (
            SCStripePreferences::$testID ??
            '48300190962813803893f1e402650c9c767a6381'
        );
    }
    /* ID() */



    /**
     * @param mixed $model
     * @param string $keyPath
     * @param bool $testonly
     *
     * @return ?string
     */
    private static function
    valueAsStripeAPIKey(
        $model,
        string $keyPath,
        bool $testonly = false
    ): ?string {
        $value = trim(CBModel::valueToString($model, $keyPath));

        if (preg_match('/^(pk_test_|sk_test_)/', $value)) {
            return $value;
        }

        if ($testonly) {
            return null;
        }

        if (preg_match('/^(pk_live_|sk_live_)/', $value)) {
            return $value;
        }

        return null;
    }
    /* valueAsStripeAPIKey() */

}
