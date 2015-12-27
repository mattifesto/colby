<?php

final class CBThemedTextViewTheme {

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return CBTheme::editorURLsForJavaScript2([
            CBThemedTextViewTheme::URL('CBThemedTextViewThemeEditorFactory.js')
        ]);
    }

    /**
     * @return null
     */
    public static function modelsWillSave(array $tuples) {
        CBTheme::modelsWillSaveWithClassName($tuples, __CLASS__);
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        return CBTheme::specToModelWithClassName($spec, __CLASS__);
    }

    /**
     * @return {stdClass}
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Themed Text View Themes',
            'singularTitle' => 'Themed Text View Theme'
        ]);
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
