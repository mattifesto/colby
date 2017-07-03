<?php

final class CBLinkView1Editor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUIImageChooser', 'CBUISelector', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
