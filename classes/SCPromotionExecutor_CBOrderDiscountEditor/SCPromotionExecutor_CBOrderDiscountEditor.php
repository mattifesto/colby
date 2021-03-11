<?php

final class
SCPromotionExecutor_CBOrderDiscountEditor {

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
                'v675.14.js',
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
    ) {
        return [
            'CBConvert',
            'CBModel',
            'CBUI',
            'CBUIBooleanEditor2',
            'CBUIStringEditor2',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
