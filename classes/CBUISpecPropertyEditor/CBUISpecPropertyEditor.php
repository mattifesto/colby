<?php

/**
 * This class edits a property that holds a spec.
 *
 * It is similar to the CBSpecArrayEditor class which does the same thing but
 * for an array of specs.
 */
final class CBUISpecPropertyEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v519.js', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIActionLink',
            'CBUINavigationView',
            'CBUISelector',
            'CBUISpec',
            'CBUISpecEditor',
        ];
    }
}
