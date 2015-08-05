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
     * Similar to `modelByID` except that if the model isn't yet cached it will
     * attempt to fetch it from the database and place it in the cache. This
     * function exists because there are times where some code may be run in a
     * context where models are cached and also in another context where they
     * aren't. Obviously the context where models aren't cached will have lower
     * performance, especially since it will be fetching models one at a time.
     *
     * An example of this is a view with dependencies which may be included in a
     * CBViewPage which caches dependencies or in a URL handler which probably
     * doesn't.
     *
     * @param   {hex160}    $ID
     *
     * @return  {stdClass} | false
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
