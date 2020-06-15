<?php

final class CBPageListView2Editor {

    /* -- CBHTMLOutput interfaces -- -- -- -- --  */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v619.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBModel',
            'CBUI',
            'CBUIStringEditor'
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
