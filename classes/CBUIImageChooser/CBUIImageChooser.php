<?php

final class CBUIImageChooser {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v485.css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v507.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */
}
/* CBUIImageChooser */
