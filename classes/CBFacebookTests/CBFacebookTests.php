<?php

final class CBFacebookTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'fetchUserProperties',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_fetchUserProperties(): stdClass {
        $currentUserID = ColbyUser::getCurrentUserCBID();

        $currentUserModel = CBModelCache::fetchModelByID(
            $currentUserID
        );

        $facebookAccessToken = CBModel::valueToString(
            $currentUserModel,
            'facebookAccessToken'
        );

        if (empty($facebookAccessToken)) {
            return (object)[
                'succeeded' => true,
                'message' => <<<EOT

                    This test was not run beacuse the current user does not have
                    a Facebook access token.

                EOT,
            ];
        }

        $facebookUserProperties = CBFacebook::fetchUserProperties(
            $facebookAccessToken
        );

        $actualKeys = array_keys(
            get_object_vars(
                $facebookUserProperties
            )
        );

        $expectedKeys = [
            'name',
            'metadata',
            'id',
        ];

        if ($actualKeys !== $expectedKeys) {
            return CBTest::resultMismatchFailure(
                'keys',
                $actualKeys,
                $expectedKeys
            );
        }

        $currentUserModelAsCBMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON($currentUserModel)
        );

        $facebookUserPropertiesAsCBMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON($facebookUserProperties)
        );

        return (object)[
            'succeeded' => true,
            'message' => <<<EOT

                --- pre\n{$facebookUserPropertiesAsCBMessage}
                ---

                --- pre\n{$currentUserModelAsCBMessage}
                ---

            EOT,
        ];
    }
    /* CBTest_fetchUserProperties() */

}
