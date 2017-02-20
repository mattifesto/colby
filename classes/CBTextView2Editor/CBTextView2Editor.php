<?php

final class CBTextView2Editor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIBooleanEditor', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
