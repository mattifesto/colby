<?php

final class CBSitePreferencesTests {

    /**
     * @return null
     */
    public static function test() {
        $value = CBSitePreferences::debug();

        if (!is_bool($value)) {
            throw new Exception('`CBSitePreferences::debug()` should return a boolean value.');
        }
    }
}
