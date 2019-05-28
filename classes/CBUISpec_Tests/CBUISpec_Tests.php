<?php

final class CBUISpec_Tests {

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
     * @TODO 2019_05_28
     *
     *      Eventually we want to have all model editors registered and fetch
     *      them here by using an API.
     *
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBTest',
            'CBUISpec',

            'CBBackgroundViewEditor',
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
                'title' =>
                'CBUISpec.specToDescription() tests for well-known models',

                'name' => 'specToDescription',
            ],
            (object)[
                'title' =>
                'CBUISpec.specToThumbnailURI() tests for well-known models',

                'name' => 'specToThumbnailURI',
            ],
        ];
    }
    /* CBTest_getTests() */
}
/* CBUISpec_Tests */
