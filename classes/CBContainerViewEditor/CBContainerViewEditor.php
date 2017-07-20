<?php

final class CBContainerViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIBooleanEditor', 'CBUIImageChooser', 'CBUISelector', 'CBUISpec', 'CBUIStringEditor', 'CBUIThemeSelector'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [[string, mixed]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBContainerViewAddableViews', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }
}
