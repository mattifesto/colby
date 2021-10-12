<?php

/**
 * @NOTE 2021_10_05
 *
 *      This class creates container elements for text and other readable
 *      content. Forms can be placed inside and so can basically anything else,
 *      but the container will always have a width that is appropriate for text.
 */
final class
CB_Brick_TextContainer {

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
