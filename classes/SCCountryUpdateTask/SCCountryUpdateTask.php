<?php

final class
SCCountryUpdateTask
{
    // CBTasks2 interfaces



    /**
     * @param CBID $CBID
     *
     * @return object|null
     */
    static function
    CBTasks2_run(
        string $CBID
    ): ?stdClass
    {
        $associationKey =
        'SCCountry_isActive';

        $tags =
        [
            'true',
        ];

        $countryModel =
        CBModelCache::fetchModelByID(
            $CBID
        );

        $isActive =
        CBModel::valueToBool(
            $countryModel,
            'isActive'
        );

        /* delete previous tags */

        CBTag::delete(
            $CBID,
            $associationKey,
            $tags
        );

        /* add current tags */
        if (
            !empty($countryModel) &&
            $isActive
        ) {
            CBTag::create(
                $tags
            );

            CBTag::add(
                $CBID,
                $associationKey,
                $tags
            );
        }

        return null;
    }
    // CBTasks2_run()

}
