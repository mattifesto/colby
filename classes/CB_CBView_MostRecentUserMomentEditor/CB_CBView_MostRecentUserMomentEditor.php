<?php

final class
CB_CBView_MostRecentUserMomentEditor {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        $arrayOfJavaScriptURLs =
        [
            Colby::flexpath(
                __CLASS__,
                'v675.51.js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CB_Brick_TextContainer',
            'CB_UI',
            'CB_CBView_MostRecentUserMoment',
            'CBAjax',
            'CBConvert',
            'CBErrorHandler',
            'CBModel',
            'CBUIStringEditor2',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
