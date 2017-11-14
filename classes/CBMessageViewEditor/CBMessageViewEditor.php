<?php

final class CBMessageViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBMessageMarkup', 'CBUI', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
