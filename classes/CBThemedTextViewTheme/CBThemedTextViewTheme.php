<?php

final class CBThemedTextViewTheme {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [ CBThemedTextViewTheme::URL('CBThemedTextViewThemeEditor.css') ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBResponsiveEditorFactory.js',
            CBThemedTextViewTheme::URL('CBThemedTextViewThemeEditorFactory.js')
        ];
    }

    /**
     * @return null
     */
    public static function modelsWillSave(array $tuples) {
        array_walk($tuples, function($tuple) {
            CBDataStore::makeDirectoryForID($tuple->model->ID);
            $filepath       = CBDataStore::filepath([
                'ID'        => $tuple->model->ID,
                'filename'  => 'CBThemedTextViewTheme.css'
            ]);
            file_put_contents($filepath, $tuple->model->styles);
        });
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $template = isset($spec->styles) ? $spec->styles : '';
        $title = isset($spec->title) ? trim($spec->title) : '';
        $model->styles = CBThemedTextViewTheme::templateToStyles($template, $spec->ID, $title);

        return $model;
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
     * This function replaces the strings "view" or ".view" with the CSS
     * class name for the CBThemedTextViewTheme. The string "\view" will not be
     * replaced.
     *
     * @return {string}
     */
    private static function templateToStyles($template, $ID, $title) {
        $keyword    = 'view';
        $escape     = "\\\\{$keyword}";
        $hash       = sha1($escape);
        $selector   = ".T{$ID}";
        $styles     = $template;
        $styles     = preg_replace("/{$escape}/", $hash, $styles);
        $styles     = preg_replace("/\\.?{$keyword}/", $selector, $styles);
        $styles     = preg_replace("/{$hash}/", $keyword, $styles);
        $styles     = "{$styles}\n\n/**\n * Styles for the \"{$title}\" CBThemedTextViewTheme\n */\n";
        return $styles;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
