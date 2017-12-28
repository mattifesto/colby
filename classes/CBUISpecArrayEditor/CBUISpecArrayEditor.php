<?php

final class CBUISpecArrayEditor {

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
        return ['CBUICommandPart', 'CBUISelectableItem',
                'CBUISelectableItemContainer', 'CBUISelector',
                'CBUISpecClipboard', 'CBUISpecEditor',
                'CBUITitleAndDescriptionPart'];
    }
}
