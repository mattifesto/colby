<?php

/**
 * @deprecated 2017.09.04 This file can be removed after install has been run on
 *             all websites.
 */
final class CBDataStoreAdmin {

    static function ID() {
        return '5bda1825fe0be9524106061b910fd0b8e1dde0c2';
    }

    static function install() {
        CBDataStore::deleteByID(CBDataStoreAdmin::ID());
    }
}
