<?php

final class CBViewPageEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUINavigationView', 'CBUISpecEditor', 'CBUISpecSaver', 'CBViewPageInformationEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
