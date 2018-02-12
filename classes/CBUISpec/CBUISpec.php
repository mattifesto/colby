<?php

/**
 * This class provides JavaScript spec related helper functions.
 */
final class CBUISpec {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v387.js', cbsysurl())];
    }
}
