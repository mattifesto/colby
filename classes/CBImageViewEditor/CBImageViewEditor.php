<?php

final class CBImageViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }
}
