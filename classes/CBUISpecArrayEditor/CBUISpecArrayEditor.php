<?php

final class CBUISpecArrayEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v361.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v601.js', cbsysurl())
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUICommandPart',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISelectableItem',
            'CBUISelectableItemContainer',
            'CBUISelector',
            'CBUISpec',
            'CBUISpecClipboard',
            'CBUISpecEditor',
            'CBUIThumbnailPart',
            'CBUITitleAndDescriptionPart',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBUISpecArrayEditor */
