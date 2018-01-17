<?php

final class CBDefaultEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUISectionItem4'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v368.js', cbsysurl())];
    }
}
