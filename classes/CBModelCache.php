<?php

final class CBModelCache {

    private static $cache = [];
    private static $neededModelIDs = [];

    /**
     * This is the only function in this class that will query the database
     * directly.
     *
     * @param   [{hex160}]  $IDs
     *
     * @return  null
     */
    public static function cacheModelsByID(array $IDs) {
        $IDsToFetch = array_unique(array_merge($IDs, self::$neededModelIDs));
        $IDsToFetch = array_filter($IDsToFetch, function($ID) {
            return CBModelCache::modelByID($ID) === false;
        });

        if (!empty($IDsToFetch)) {
            CBModelCache::$cache = array_merge(CBModelCache::$cache, CBModels::fetchModelsByID($IDsToFetch));
        }

        self::$neededModelIDs = [];
    }

    /**
     * This is the function to call if you need a model now. If the model isn't
     * in the cache a query will be run to fetch the requested model and all
     * other needed models.
     *
     * If the model doesn't exist in the database then false will be returned.
     *
     * @param hex160 $ID
     *
     * @return stdClass|false
     */
    public static function fetchModelByID($ID) {
        CBModelCache::cacheModelsByID([$ID]);
        return CBModelCache::modelByID($ID);
    }

    /**
     * @return null
     */
    public static function fetchModelLazilyByID($ID) {
        self::$neededModelIDs[] = $ID;
    }

    /**
     * @return null
     */
    public static function fetchModelsLazilyByID(array $IDs) {
        self::$neededModelIDs = array_merge(self::$neededModelIDs, $IDs);
    }

    /**
     * @param   {hex160}    $ID
     *
     * @return  {stdClass} | false
     *  Returns the model if it's cached; otherwise false.
     */
    public static function modelByID($ID) {
        return isset(CBModelCache::$cache[$ID]) ? CBModelCache::$cache[$ID] : false;
    }

    /**
     * @return null
     */
    public static function uncacheModelsByID(array $IDs) {
        CBModelCache::$cache = array_filter(CBModelCache::$cache, function($key) use ($IDs) {
            return isset($IDs[$key]);
        }, ARRAY_FILTER_USE_KEY);
    }
}
