<?php

final class CBFacebookPreferences {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBModelUpdater::update(
            (object)[
                'className' => 'CBFacebookPreferences',
                'ID' => CBFacebookPreferences::getModelCBID(),
            ]
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModelUpdater',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[
            'appID' => trim(
                CBModel::valueToString(
                    $spec,
                    'appID'
                )
            ),

            'appSecret' => trim(
                CBModel::valueToString(
                    $spec,
                    'appSecret'
                )
            ),
        ];
    }
    /* CBModel_build() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return string
     *
     *      Returns an empty string if there is not app ID.
     */
    static function getAppID(): string {
        $model = CBModelCache::fetchModelByID(
            CBFacebookPreferences::getModelCBID()
        );

        $appID = CBModel::valueToString(
            $model,
            'appID'
        );

        if ($appID !== '') {
            return $appID;
        }

        return '';
    }
    /* getAppID() */



    /**
     * @return string
     *
     *      Returns an empty string if there is no app secret.
     */
    static function getAppSecret(): string {
        $model = CBModelCache::fetchModelByID(
            CBFacebookPreferences::getModelCBID()
        );

        $appSecret = CBModel::valueToString(
            $model,
            'appSecret'
        );

        if ($appSecret !== '') {
            return $appSecret;
        }

        return '';
    }
    /* getAppSecret() */



    /**
     * @return CBID
     */
    static function getModelCBID(): string {
        return 'e7e73278178d6f1f8a6726697766ceff0ee8798c';
    }

}
