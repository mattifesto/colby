<?php

final class CBContainerViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIImageChooser', 'CBUISelector',
                'CBUISpec', 'CBUIStringEditor'];
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
