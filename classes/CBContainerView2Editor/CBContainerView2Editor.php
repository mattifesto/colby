<?php

final class CBContainerView2Editor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUIImageChooser', 'CBUISpecArrayEditor', 'CBUIStringEditor'];
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
            ['CBContainerView2Editor_addableClassNames', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }
}
