<?php

/**
 * See documentation.
 */
final class
CBTest_CBGoogleAnalytics
{

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'sendViewItemEvent',
                'type' => 'CBTest_type_interactive_client',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.13.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBGoogleAnalytics',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
