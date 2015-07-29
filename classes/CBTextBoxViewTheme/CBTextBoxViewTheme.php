<?php

final class CBTextBoxViewTheme {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [ CBTextBoxViewTheme::URL('CBTextBoxViewThemeEditor.css') ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBTextBoxViewTheme::URL('CBTextBoxViewThemeEditorFactory.js')
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
                'filename'  => 'theme.css'
            ]);
            file_put_contents($filepath, $tuple->model->styles);
        });
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model          = CBModels::modelWithClassName(__CLASS__);
        $template       = isset($spec->styles) ? $spec->styles : '';
        $model->styles  = CBTextBoxViewTheme::templateToStyles($template, $spec->ID, $spec->title);

        return $model;
    }

    /**
     * This function replaces the strings "textbox" or ".textbox" with the CSS
     * class name for the CBTextBoxViewTheme. The string "\textbox" will not be
     * replaced.
     *
     * @return {string}
     */
    private static function templateToStyles($template, $ID, $title = '') {
        $keyword    = 'textbox';
        $escape     = "\\\\{$keyword}";
        $hash       = sha1($escape);
        $selector   = ".T{$ID}";
        $styles     = $template;
        $styles     = preg_replace("/{$escape}/", $hash, $styles);
        $styles     = preg_replace("/\\.?{$keyword}/", $selector, $styles);
        $styles     = preg_replace("/{$hash}/", $keyword, $styles);
        $styles     = "{$styles}\n\n/**\n * Styles for the \"{$title}\" CBTextBoxViewTheme\n */\n";
        return $styles;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBTextBoxViewTheme/{$filename}";
    }
}
