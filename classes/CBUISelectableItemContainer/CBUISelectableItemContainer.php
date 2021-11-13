<?php

final class
CBUISelectableItemContainer {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.41.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v371.js', cbsysurl())
        ];
    }



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CB_UI',
            'CBUICommandPart',
            'CBUIStringsPart'
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
