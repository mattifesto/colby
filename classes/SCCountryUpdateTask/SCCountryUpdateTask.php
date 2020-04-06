<?php

final class SCCountryUpdateTask {

    /**
     * @param ID $ID
     *
     * @return ?object
     */
    static function CBTasks2_run(string $ID): ?stdClass {
        $associationKey = 'SCCountry_isActive';
        $tags = ['true'];
        $countryModel = CBModelCache::fetchModelByID($ID);
        $isActive = CBModel::valueToBool($countryModel, 'isActive');

        /* delete previous tags */
        CBTag::delete(
            $ID,
            $associationKey,
            $tags
        );

        /* add current tags */
        if (!empty($countryModel)) {
            CBTag::create($tags);

            CBTag::add(
                $ID,
                $associationKey,
                $tags
            );
        }

        return null;
    }
}
