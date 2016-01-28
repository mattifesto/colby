<?php

final class CBContainerViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIBooleanEditor', 'CBUIImageSizeView', 'CBUIImageUploader', 'CBUIImageView', 'CBUIStringEditor', 'CBUIThemeSelector'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBContainerViewEditor::URL('CBContainerViewEditor.js')];
    }

    /**
     * @return [[string, mixed]]
     */
    public static function requiredJavaScriptVariables() {
        return [
            ['CBContainerViewAddableViews', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
