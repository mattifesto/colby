<?php

final class CBContainerViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIImageChooser', 'CBUISelector',
                'CBUISpec', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }

    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBContainerViewAddableViews', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }
}
