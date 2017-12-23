<?php

/**
 * This class edits a property that holds a spec.
 *
 * It is similar to the CBArrayEditor class which does the same thing but for an
 * array of specs.
 */
final class CBUISpecPropertyEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIActionLink', 'CBUISpec'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }
}
