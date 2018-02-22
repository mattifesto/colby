<?php

final class CBUISelector {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUINavigationArrowPart', 'CBUINavigationView',
                'CBUISectionItem4', 'CBUITitleAndDescriptionPart'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v393.js', cbsysurl())];
    }
}
