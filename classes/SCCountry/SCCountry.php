<?php

final class
SCCountry
{
    // CBModel interfaces



    /**
     * @param object $countrySpec
     *
     * @return ?object
     */
    static function
    CBModel_build(
        stdClass $countrySpec
    ): stdClass
    {
        $countryModel =
        (object)
        [
            'isActive' =>
            CBModel::valueToBool(
                $countrySpec,
                'isActive'
            ),

            'isDefault' =>
            CBModel::valueToBool(
                $countrySpec,
                'isDefault'
            ),

            'moniker' =>
            CBModel::valueAsMoniker(
                $countrySpec,
                'moniker'
            ),
        ];

        SCCountry::setTitle(
            $countryModel,
            SCCountry::getTitle(
                $countrySpec
            )
        );

        return $countryModel;
    }
    // CBModel_build()



    /**
     * @param object $countryModel
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $countryModel
    ): string
    {
         $title =
         SCCountry::getTitle(
             $countryModel
         );

         return $title;
    }
    // CBModel_getTitle()



    /**
     * @param object $spec
     *
     * @return CBID|null
     */
    static function
    CBModel_toID(
        stdClass $spec
    ): ?string
    {
        $moniker =
        CBModel::valueAsMoniker(
            $spec,
            'moniker'
        );

        if (
            $moniker === null
        ) {
            return null;
        }

        else
        {
            $CBID =
            SCCountry::monikerToID(
                $moniker
            );

            return $CBID;
        }
    }
    // CBModel_toID()



    /**
     * @param object $countryModel
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $countryModel
    ): string
    {
        $title =
        SCCountry::getTitle(
            $countryModel
        );

        return $title;
    }
    // CBModel_toSearchText()



    /**
     * @param object $upgradableCountryModel
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $upgradableCountryModel
    ): stdClass
    {
        /**
         * 2022_07_28_1658973516
         *
         *      CBModel_toSearchText() was implemented.
         *      A bug was fixed in SCCountryUpdateTask.
         */

        CBModel::setProcessVersionNumber(
            $upgradableCountryModel,
            '2022_07_28_1658973516'
        );

        return $upgradableCountryModel;
    }
    // CBModel_upgrade()



    // CBModels interfaces



    /**
     * @param [string] $modelCBIDs
     *
     * @return void
     */
    static function
    CBModels_willDelete(
        array $modelCBIDs
    ): void
    {
        CBTasks2::restart(
            'SCCountryUpdateTask',
            $modelCBIDs
        );
    }
    // CBModels_willDelete()



    /**
     * @param [object] $models
     *
     * @return void
     */
    static function
    CBModels_willSave(
        array $models
    ): void
    {
        $modelCBIDs =
        array_map(
            function (
                $model
            ): string
            {
                $CBID =
                CBModel::getCBID(
                    $model
                );

                return $CBID;
            },
            $models
        );

        CBTasks2::restart(
            'SCCountryUpdateTask',
            $modelCBIDs
        );
    }
    // CBModels_willSave()



    // accessors



    /**
     * @param object $countryModel
     *
     * @return string
     */
    static function
    getTitle(
        stdClass $countryModel
    ): string
    {
        $title =
        trim(
            CBModel::valueToString(
                $countryModel,
                'title'
            )
        );

        return $title;
    }
    // getTitle()



    /**
     * @param object $countryModel
     * @param string $newTitle
     *
     * @return void
     */
    static function
    setTitle(
        stdClass $countryModel,
        string $newTitle
    ): void
    {
        $countryModel->title =
        $newTitle;
    }
    // setTitle()



    // functions



    /**
     * @param string $moniker
     *
     * @return CBID
     */
    static function
    monikerToID(
        string $moniker
    ): string
    {
        $CBID =
        sha1(
            "96e6dabb16069a8d53fd66f58430c6ac8e0f7a40 {$moniker}"
        );

        return $CBID;
    }
    // monikerToID()

}
