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
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }
}
