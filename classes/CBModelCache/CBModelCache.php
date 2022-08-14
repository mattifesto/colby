<?php

final class
CBModelCache
{
    private static $cache =
    [];

    private static $neededModelIDs =
    [];



    // -- CBCodeAdmin interfaces



    /**
     * @return [object]
     */
    static function
    CB_CBAdmin_Code_getDeprecatedCodeSearches(
    ): array
    {
        $searches =
        [];



        // CBModelCache::fetchModelByID()

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '8fbcc9332b71e3c7527d1a18ea9966aca0dd9ebd'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_08_14_1660511945'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use CBModelCache::fetchModelByCBID().

        EOT;

        $codeSearchSpec->regex =
        '\bCBModelCache::fetchModelByID\b';

        $codeSearchSpec->severity =
        5;

        $codeSearchSpec->title =
        'CBModelCache::fetchModelByID()';

        array_push(
            $searches,
            $codeSearchSpec
        );



        return $searches;
    }
    /* CBCodeAdmin_searches() */



    // functions



    /**
     * This is the only function in this class that will query the database
     * directly.
     *
     * @param [ID] $IDs
     *
     * @return void
     */
    static function cacheModelsByID(array $IDs): void {
        $IDsToFetch = array_unique(
            array_merge(
                $IDs,
                CBModelCache::$neededModelIDs
            )
        );

        $IDsToFetch = array_filter(
            $IDsToFetch,
            function($ID) {
                return empty(CBModelCache::modelByID($ID));
            }
        );

        if (!empty($IDsToFetch)) {
            CBModelCache::$cache = array_merge(
                CBModelCache::$cache,
                CBModels::fetchModelsByID($IDsToFetch)
            );
        }

        CBModelCache::$neededModelIDs = [];
    }



    /**
     * This is the function to call if you need a model now. If the model isn't
     * in the cache a query will be run to fetch the requested model and all
     * other needed models.
     *
     * If the model doesn't exist in the database then null will be returned.
     *
     * @param CBID $modelCBID
     *
     * @return object|null
     */
    static function
    fetchModelByCBID(
        string $modelCBID
    ): ?stdClass
    {
        CBModelCache::cacheModelsByID(
            [$modelCBID]
        );

        $model =
        CBModelCache::modelByID(
            $modelCBID
        );

        return $model;
    }
    // fetchModelByCBID()



    /**
     * @deprecated 2022_08_14
     *
     *      Use CBModelCache::fetchModelByCBID()
     */
    static function
    fetchModelByID(
        string $modelCBID
    ): ?stdClass
    {
        $model =
        CBModelCache::fetchModelByCBID(
            $modelCBID
        );

        return $model;
    }
    // fetchModelByID



    /**
     * @return void
     */
    static function fetchModelLazilyByID($ID): void {
        CBModelCache::$neededModelIDs[] = $ID;
    }



    /**
     * @param [ID] $IDs
     *
     * @return [model]
     *
     *      If no model exists for an ID there will be no item in the returned
     *      array for that ID.
     */
    static function fetchModelsByID(array $IDs): array {
        CBModelCache::cacheModelsByID($IDs);

        $models = [];

        foreach ($IDs as $ID) {
            $model = CBModelCache::modelByID($ID);

            if (!empty($model)) {
                array_push(
                    $models,
                    $model
                );
            }
        }

        return $models;
    }



    /**
     * @return void
     */
    static function fetchModelsLazilyByID(array $IDs): void {
        CBModelCache::$neededModelIDs = array_merge(
            CBModelCache::$neededModelIDs,
            $IDs
        );
    }



    /**
     * @param ID $ID
     *
     * @return ?model
     *
     *      Returns the model if it's cached; otherwise null.
     */
    static function modelByID($ID): ?stdClass {
        return (
            isset(CBModelCache::$cache[$ID]) ?
            CBModelCache::$cache[$ID] :
            null
        );
    }



    /**
     * @param [ID] $IDs
     *
     * @return void
     */
    static function uncacheByID(array $IDs): void {
        CBModelCache::$cache = array_filter(
            CBModelCache::$cache,
            function ($key) use ($IDs) {
                return isset($IDs[$key]);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

}
