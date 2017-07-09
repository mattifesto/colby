<?php

final class CBUINavigationView {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUISpecEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
