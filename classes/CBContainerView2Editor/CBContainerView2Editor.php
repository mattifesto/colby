<?php

final class CBContainerView2Editor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUIImageChooser', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [[string, mixed]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBContainerView2EditorAddableViews', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }
}
