<?php

final class
SCCountry
{
    // CBModel interfaces



    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass
    {
        $countryModel =
        (object)
        [
            'isActive' =>
            CBModel::valueToBool(
                $spec,
                'isActive'
            ),

            'isDefault' =>
            CBModel::valueToBool(
                $spec,
                'isDefault'
            ),

            'moniker' =>
            CBModel::valueAsMoniker(
                $spec,
                'moniker'
            ),

            'title' =>
            trim(
                CBModel::valueToString(
                    $spec,
                    'title'
                )
            ),
        ];

        return $countryModel;
    }
    // CBModel_build()



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
