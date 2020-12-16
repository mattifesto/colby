<?php

final class CBUIPanel_Tests {

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
                'v675.5.js',
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
            'CBConvert',
            'CBModel',
            'CBTest',
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
                'name' => 'confirmText_cancel',
                'title' => 'CBUIPanel confirmText() cancellation',
            ],
            (object)[
                'name' => 'confirmText_confirm',
                'title' => 'CBUIPanel confirmText() confirmation',
            ],
            (object)[
                'name' => 'confirmText_interactive',
                'title' => 'CBUIPanel confirmText()',
                'type' => 'interactive',
            ],
            (object)[
                'name' => 'displayAjaxResponse_threeTimes',
            ],
            (object)[
                'name' => 'displayAjaxResponse_threeTimes_interactive',
                'type' => 'interactive',
            ],
            (object)[
                'name' => 'displayElement_alreadyDisplayedError',
                'title' => 'CBUIPanel.displayElement() already displayed error',
            ],
            (object)[
                'name' => 'displayElementThreeTimes_interactive',
                'type' => 'interactive',
            ],
            (object)[
                'name' => 'displayError',
            ],
            (object)[
                'name' => 'displayError_interactive',
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
