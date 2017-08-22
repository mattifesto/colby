<?php

/**
 * This class edits a property holding an array of specs and should potentially
 * be named CBUISpecArrayEditor or CBUISpecArrayPropertyEditor.
 *
 * It is similar to the CBUISpecPropertyEditor which does the same thing but
 * for a single spec instead of an array.
 */
final class CBArrayEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIActionLink', 'CBUISpec', 'CBUISpecEditor'];
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
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }
}
