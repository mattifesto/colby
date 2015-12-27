<?php

/**
 * This class is a set of helper functions that can be used by actual themes.
 */
final class CBTheme {

    public static function compareModels($model1, $model2) {
        $result = strcmp($model1->classNameForKind, $model2->classNameForKind);

        if ($result === 0) {
            return strcmp($model1->title, $model2->title);
        } else {
            return $result;
        }
    }

    /**
     * @deprecated Transition from custom theme to CBTheme
     *
     * @return [string]
     */
    public static function editorURLsForJavaScript2(array $URLs = []) {
        return array_merge([
            CBSystemURL . '/javascript/CBResponsiveEditorFactory.js',
            CBSystemURL . '/javascript/CBThemeEditorFactory.js',
        ], $URLs);
    }

    /**
     * @param hex160 $ID
     *
     * @return string
     */
    public static function IDToCSSClass($ID) {
        if (empty($ID)) {
            return "NoTheme";
        } else {
            return "T{$ID}";
        }
    }

    /**
     * @param hex160 $ID
     *
     * @return string
     */
    public static function IDToCSSURL($ID) {
        if (empty($ID)) {
            return null;
        } else {
            return CBDataStore::toURL(['ID' => $ID, 'filename' => 'CBTheme.css']);
        }
    }

    /**
     * @return stdClass
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Themes',
            'singularTitle' => 'Theme'
        ]);
    }

    /**
     * @param string $model->classNameForKind
     *
     * @return string
     */
    public static function modelToSummaryText(stdClass $model) {
        return $model->classNameForKind;
    }

    /**
     * @return null
     */
    public static function modelsWillSave(array $tuples) {
        CBTheme::modelsWillSaveWithClassName($tuples, __CLASS__);
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
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = CBTheme::specToModelWithClassName($spec, __CLASS__);
        $model->classNameForKind = isset($spec->classNameForKind) ? $spec->classNameForKind : null;
        $model->description = isset($spec->description) ? $spec->description : null;

        return $model;
    }

    /**
     * @return stdClass
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
     * @return string
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
