<?php

final class CBUISpecEditor_Tests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v474.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @TODO 2019_05_27
     *
     *      Eventually we want to have all model editors registerd and fetch
     *      them here by using an API.
     *
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBTest',
            'CBUISpecEditor',

            'CBArtworkViewEditor',
            'CBViewPageEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'title' => 'CBUISpecEditor tests for well-known models',
                'name' => 'wellKnownModels',
            ],
        ];
    }
    /* CBTest_getTests() */
}
/* CBUISpecEditor_Tests */
