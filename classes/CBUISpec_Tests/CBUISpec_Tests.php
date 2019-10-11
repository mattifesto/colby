<?php

final class CBUISpec_Tests {


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
                'name' => 'specToDescription',
                'title' => (
                    'CBUISpec.specToDescription() tests for well-known models'
                ),
            ],
            (object)[
                'name' => 'specToThumbnailURL',
                'title' => (
                    'CBUISpec.specToThumbnailURL() tests for well-known models'
                ),
            ],
        ];
    }
    /* CBTest_getTests() */

}
/* CBUISpec_Tests */
