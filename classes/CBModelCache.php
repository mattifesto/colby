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
            $IDs = array_filter($IDs, function($ID) { return !CBModelCache::modelIsCachedByID($ID); });
        }

        CBModelCache::$cache = array_merge(CBModelCache::$cache, CBModels::fetchModelsByID($IDs));
    }

    /**
     * @param   {hex160}    $ID
     *
     * @return  {stdClass} | false
     */
    public static function modelByID($ID) {
        return isset(CBModelCache::$cache[$ID]) ? CBModelCache::$cache[$ID] : false;
    }

    /**
     * @return {boolean}
     */
    public static function modelIsCachedByID($ID) {
        return isset(CBModelCache::$cache[$ID]);
    }
}
