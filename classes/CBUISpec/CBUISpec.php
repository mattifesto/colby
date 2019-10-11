<?php

/**
 * This class provides JavaScript spec related helper functions.
 */
final class CBUISpec {


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v537.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBConvert'.
            'CBImage',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBUISpec */
