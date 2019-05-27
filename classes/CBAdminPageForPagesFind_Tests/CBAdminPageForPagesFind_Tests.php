<?php

final class CBAdminPageForPagesFind_Tests {

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
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBAdminPageForPagesFind',
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
                'CBAdminPageForPagesFind / CBPageList.createElement()',

                'name' => 'CBPageList_createElement',
            ],
        ];
    }
    /* CBTest_getTests() */
}
/* CBAdminPageForPagesFind_Tests */
