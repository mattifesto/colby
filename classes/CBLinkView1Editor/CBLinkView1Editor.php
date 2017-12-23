<?php

final class CBLinkView1Editor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUIImageChooser', 'CBUISelector', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }
}
