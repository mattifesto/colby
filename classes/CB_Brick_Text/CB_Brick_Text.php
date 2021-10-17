<?php

/**
 * @NOTE 2021_10_02
 *
 *      This class was created to be the authority for rendering text for the
 *      Colby user interface. The styles and methods for rendering text have
 *      been convoluted from the very start, this class will clarify them.
 */
final class
CB_Brick_Text {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.38.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.38.js',
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
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
