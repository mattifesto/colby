<?php

final class CBUISpecEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v572.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBDefaultEditor',
            'CBException',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBUISpecEditor */
