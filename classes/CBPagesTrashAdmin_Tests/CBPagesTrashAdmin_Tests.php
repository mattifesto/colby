<?php

final class CBPagesTrashAdmin_Tests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v556.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBException',
            'CBTest',
            'Colby',
        ];
    }



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'ajax',
            ],
        ];
    }
    /* CBTest_getTests() */

}
