<?php

final class CBContainerViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIImageChooser', 'CBUISelector', 'CBUISpec',
                'CBUISpecArrayEditor', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v366.js', cbsysurl())];
    }

    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBContainerViewEditor_addableClassNames', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }
}
