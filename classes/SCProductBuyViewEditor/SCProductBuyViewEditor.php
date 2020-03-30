<?php

final class SCProductBuyViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v144.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBModel',
            'CBUI',
            'CBUIBooleanSwitchPart',
            'CBUIStringEditor'
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


}
/* SCProductBuyViewEditor */
