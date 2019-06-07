<?php

final class CBUISpec_Tests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v477.js', cbsysurl()),
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
            'CBModel',
            'CBTest',
            'CBUISpec',

            'CBBackgroundViewEditor',
            'CBContainerViewEditor',
            'CBContainerView2Editor',
            'CBIconLinkViewEditor',
            'CBLinkView1Editor',
            'CBMenuViewEditor',
            'CBPageListView2Editor',
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
                'CBUISpec.specToThumbnailURL() tests for well-known models',

                'name' => 'specToThumbnailURL',
            ],
        ];
    }
    /* CBTest_getTests() */
}
/* CBUISpec_Tests */
