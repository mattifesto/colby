<?php

final class CBUIPanel_Tests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v525.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIPanel',
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
                'name' => 'deprecated',
                'title' => 'CBUIPanel deprecated',
                'type' => 'interactive',
            ],
            (object)[
                'name' => 'displayElementThreeTimes',
                'title' => 'CBUIPanel.displayElement() three times',
                'type' => 'interactive',
            ],
            (object)[
                'name' => 'displayError',
                'title' => 'CBUIPanel.displayError()',
                'type' => 'interactive',
            ],
            (object)[
                'name' => 'displayTextThreeTimes',
                'title' => 'CBUIPanel.displayText() three times',
                'type' => 'interactive',
            ],
        ];
    }
    /* CBTest_getTests() */
}
/* CBUIPanel_Tests */
