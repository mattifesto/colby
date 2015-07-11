<?php

final class CBModelCache {

    private static $cache = [];

    /**
     * @param   [{hex160}]  $IDs
     * @param   {boolean}   $clobber
     *  If true, any requested models that already exist in the cache will be
     *  replaced by the version in the database.
     *
     * @return  null
     */
    public static function cacheModelsByID(array $IDs, array $args = []) {
        $clobber = false;
        extract($args, EXTR_IF_EXISTS);

        if (!$clobber) {
            $IDs = array_filter($IDs, function($ID) { return CBModelCache::modelByID($ID) === false; });
        }

        if (!empty($IDs)) {
            CBModelCache::$cache = array_merge(CBModelCache::$cache, CBModels::fetchModelsByID($IDs));
        }
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
     * @param   {hex160}    $ID
     *
     * @return  {stdClass} | false
     *  Returns the model if it's cached; otherwise false.
     */
    public static function modelByID($ID) {
        return isset(CBModelCache::$cache[$ID]) ? CBModelCache::$cache[$ID] : false;
    }
}
