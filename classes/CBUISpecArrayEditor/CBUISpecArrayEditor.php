<?php

final class CBUISpecArrayEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v361.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v361.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUICommandPart', 'CBUINavigationArrowPart',
                'CBUISelectableItem', 'CBUISelectableItemContainer',
                'CBUISelector', 'CBUISpec', 'CBUISpecClipboard',
                'CBUISpecEditor', 'CBUIThumbnailPart',
                'CBUITitleAndDescriptionPart'];
    }
}
