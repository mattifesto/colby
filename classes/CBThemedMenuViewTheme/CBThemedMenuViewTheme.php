<?php

final class CBThemedMenuViewTheme {

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return CBTheme::editorURLsForJavaScript([
            CBThemedMenuViewTheme::URL('CBThemedMenuViewThemeEditorFactory.js')
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
            'pluralTitle' => 'Themed Menu View Themes',
            'singularTitle' => 'Themed Menu View Theme'
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
