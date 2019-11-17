<?php

final class CBUser {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v500.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $userNumericID = CBModel::valueAsInt(
            $spec,
            'userNumericID'
        );

        if ($userNumericID === null) {
            throw CBException::createModelIssueException(
                'The "userNumericID" property must be an integer.',
                $spec,
                'd3cc11bb7ee84089207dc00fee71ecbb5f9679d4'
            );
        }

        $facebook = CBModel::valueAsObject(
            $spec,
            'facebook'
        );

        if ($facebook !== null) {
            $facebook = CBModel::clone($facebook);
        }

        return (object)[
            'description' => trim(
                CBModel::valueToString($spec, 'description')
            ),

            /**
             * @deprecated 2019_11_12
             *
             *      This property has been replaced by the facebookName and
             *      facebookUserID properties. When deprecated,
             *      CBModel_upgrade() was changed to extract the values of this
             *      object from the spec. Therefore this property can be
             *      completely removed in a few months.
             */
            'facebook' => $facebook,

            'facebookAccessToken' => CBModel::valueToString(
                $spec,
                'facebookAccessToken'
            ),

            'facebookName' => trim(
                CBModel::valueToString(
                    $spec,
                    'facebookName'
                )
            ),

            'facebookUserID' => CBModel::valueAsInt(
                $spec,
                'facebookUserID'
            ),

            'lastLoggedIn' => CBModel::valueAsInt(
                $spec,
                'lastLoggedIn'
            ) ?? 0,

            'title' => trim(
                CBModel::valueToString($spec, 'title')
            ),

            'userNumericID' => $userNumericID,
        ];
    }
    /* CBModel_build() */



    /**
     * @param object $originalSpec
     *
     * @return object
     */
    static function CBModel_upgrade(stdClass $originalSpec): stdClass {
        $upgradedSpec = CBModel::clone($originalSpec);


        /* userID -> userNumericID */

        if (
            !isset($upgradedSpec->userNumericID)
        ) {
            $upgradedSpec->userNumericID = CBModeL::valueAsInt(
                $upgradedSpec,
                'userID'
            );
        }


        /* remove userID property */

        unset($upgradedSpec->userID);


        /* facebook.id -> facebookUserID */

        if (
            !isset($upgradedSpec->facebookUserID)
        ) {
            $upgradedSpec->facebookUserID = CBModel::valueAsInt(
                $upgradedSpec,
                'facebook.id'
            );
        }


        /* facebook.name -> facebookName */

        if (
            !isset($upgradedSpec->facebookName)
        ) {
            $upgradedSpec->facebookName = CBModel::valueToString(
                $upgradedSpec,
                'facebook.name'
            );
        }


        /* remove facebook property */

        unset($upgradedSpec->facebook);


        /* done */

        return $upgradedSpec;
    }
    /* CBModel_upgrade() */



    /**
     * @param [object] $models
     */
    static function CBModels_willDelete(array $userCBIDs) {
        foreach ($userCBIDs as $userCBID) {
            $userCBIDAsSQL = CBID::toSQL($userCBID);

            $SQL = <<<EOT

                DELETE FROM     ColbyUsers

                WHERE           hash = {$userCBIDAsSQL}

            EOT;

            Colby::query($SQL);

            CBModelAssociations::delete(
                null,
                'CBUserGroup_CBUser',
                $userCBID
            );

            CBModelAssociations::delete(
                $userCBID,
                'CBUser_CBUserGroup',
                null
            );
        }
    }
    /* CBModels_willDelete */

}
