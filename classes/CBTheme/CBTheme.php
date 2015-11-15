<?php

/**
 * This class is a set of helper functions that can be used by actual themes.
 */
final class CBTheme {

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript(array $URLs = []) {
        return array_merge([
            CBSystemURL . '/javascript/CBResponsiveEditorFactory.js',
            CBSystemURL . '/javascript/CBThemeEditorFactory.js',
        ], $URLs);
    }

    /**
     * @return null
     */
    public static function modelsWillSaveWithClassName(array $tuples, $className) {
        array_walk($tuples, function ($tuple) use ($className) {
            CBDataStore::makeDirectoryForID($tuple->model->ID);
            $filepath = CBDataStore::filepath([
                'ID' => $tuple->model->ID,
                'filename' => "{$className}.css",
            ]);
            file_put_contents($filepath, $tuple->model->styles);
        });
    }

    /**
     * @return {stdClass}
     */
    public static function specToModelWithClassName(stdClass $spec, $className) {
        $model = CBModels::modelWithClassName($className);
        $template = isset($spec->styles) ? $spec->styles : '';
        $title = isset($spec->title) ? trim($spec->title) : '';
        $model->styles = CBTheme::templateToStyles($template, $spec->ID, $title, $className);

        return $model;
    }

    /**
     * This function replaces the strings "view" or ".view" with the CSS
     * class name for the theme. The string "\view" will not be replaced.
     *
     * @return {string}
     */
    private static function templateToStyles($template, $ID, $title, $className) {
        $keyword = 'view';
        $escape = "\\\\{$keyword}";
        $hash = sha1($escape);
        $selector = ".T{$ID}";
        $styles = $template;
        $styles = preg_replace("/{$escape}/", $hash, $styles);
        $styles = preg_replace("/\\.?{$keyword}/", $selector, $styles);
        $styles = preg_replace("/{$hash}/", $keyword, $styles);
        $styles = "{$styles}\n\n/**\n * Styles for the \"{$title}\" {$className}\n */\n";
        return $styles;
    }
}
