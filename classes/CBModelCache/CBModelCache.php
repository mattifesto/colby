<?php

final class CBModelCache {

    private static $cache = [];
    private static $neededModelIDs = [];

    /**
     * This is the only function in this class that will query the database
     * directly.
     *
     * @param [ID] $IDs
     *
     * @return null
     */
    static function cacheModelsByID(array $IDs) {
        $IDsToFetch = array_unique(array_merge($IDs, CBModelCache::$neededModelIDs));
        $IDsToFetch = array_filter($IDsToFetch, function($ID) {
            return CBModelCache::modelByID($ID) === false;
        });

        if (!empty($IDsToFetch)) {
            CBModelCache::$cache = array_merge(CBModelCache::$cache, CBModels::fetchModelsByID($IDsToFetch));
        }

        CBModelCache::$neededModelIDs = [];
    }

    /**
     * This is the function to call if you need a model now. If the model isn't
     * in the cache a query will be run to fetch the requested model and all
     * other needed models.
     *
     * If the model doesn't exist in the database then false will be returned.
     *
     * @param ID $ID
     *
     * @return model|false
     */
    static function fetchModelByID($ID) {
        CBModelCache::cacheModelsByID([$ID]);
        return CBModelCache::modelByID($ID);
    }

    /**
     * @return void
     */
    static function fetchModelLazilyByID($ID): void {
        CBModelCache::$neededModelIDs[] = $ID;
    }

    /**
     * @return void
     */
    static function fetchModelsLazilyByID(array $IDs): void {
        CBModelCache::$neededModelIDs = array_merge(CBModelCache::$neededModelIDs, $IDs);
    }

    /**
     * @param ID $ID
     *
     * @return model|false
     *
     *      Returns the model if it's cached; otherwise false.
     */
    static function modelByID($ID) {
        return isset(CBModelCache::$cache[$ID]) ? CBModelCache::$cache[$ID] : false;
    }

    /**
     * @return void
     */
    static function uncacheModelsByID(array $IDs): void {
        CBModelCache::$cache = array_filter(CBModelCache::$cache, function ($key) use ($IDs) {
            return isset($IDs[$key]);
        }, ARRAY_FILTER_USE_KEY);
    }
}
