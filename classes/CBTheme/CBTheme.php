<?php

/**
 * This class is a set of helper functions that can be used by actual themes.
 */
final class CBTheme {

    static function compareModels($model1, $model2) {
        $result = strcmp($model1->classNameForKind, $model2->classNameForKind);

        if ($result === 0) {
            return strcmp($model1->title, $model2->title);
        } else {
            return $result;
        }
    }

    /**
     * If you have a CBTheme ID you should probably call
     * CBTheme::IDToCSSClasses() instead.
     *
     * Use caution when using this function to convert an ID that is not a
     * CBTheme ID. The function returns "NoTheme" for an empty ID which will get
     * in the way if you also have themes functionality. The workaround is to
     * only call this function if you're sure you have a non-empty ID.
     *
     * @param hex160 $ID
     *
     * @return string
     */
    static function IDToCSSClass($ID) {
        if (empty($ID)) {
            return "NoTheme";
        } else {
            return "T{$ID}";
        }
    }

    /**
     * @param hex160 $ID
     *
     * @return [string]
     */
    static function IDToCSSClasses($ID) {
        if (empty($ID)) {
            return ['NoTheme'];
        } else {
            $model = CBModelCache::fetchModelByID($ID);
            $classes = ["T{$ID}"];

            if (!empty($model->classNameForTheme)) {
                $classes[] = $model->classNameForTheme;
            }

            return $classes;
        }
    }

    /**
     * @deprecated use CBTheme::useThemeWithID()
     *
     * @param hex160 $ID
     *
     * @return string
     */
    static function IDToCSSURL($ID) {
        if (empty($ID)) {
            return null;
        } else {
            return CBDataStore::toURL(['ID' => $ID, 'filename' => 'CBTheme.css']);
        }
    }

    /**
     * @return stdClass
     */
    static function info() {
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
    static function modelToSummaryText(stdClass $model) {
        return $model->classNameForKind;
    }

    /**
     * @return null
     */
    static function modelsWillSave(array $tuples) {
        CBTheme::modelsWillSaveWithClassName($tuples, __CLASS__);
    }

    /**
     * @return null
     */
    static function modelsWillSaveWithClassName(array $tuples, $className) {
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
     * @param string? $spec->classNameForKind
     *  The class name of the object this theme styles. This is technically an
     *  optional parameter but practically required.
     * @param string? $spec->classNameForTheme
     *  This class will be required if the theme is used. It will also be added
     *  to the class names of the theme element. This allows well known themes
     *  to place their CSS in a style and also to include JavaScript.
     * @param string? $spec->description
     *  A friendly description of the theme's purpose.
     * @param string? $spec->styles
     *  The CSS for the theme in template syntax.
     * @param string? $spec->title
     *  The theme's title.
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $classNameForKind = CBModel::value($spec, 'classNameForKind');
        $ID = CBModel::valueAsID($spec, 'ID');
        $template = CBModel::value($spec, 'styles', '');
        $title = CBModel::value($spec, 'title', '', 'trim');

        return (object)[
            'className' => __CLASS__,
            'classNameForKind' => $classNameForKind,
            'classNameForTheme' => CBModel::value($spec, 'classNameForTheme', null, 'trim'),
            'description' => CBModel::value($spec, 'description'),
            'styles' => CBTheme::templateToStyles($template, $ID, $title, $classNameForKind),
            'template' => $template,
            'title' => $title,
        ];
    }

    /**
     * @deprecated use CBView::localCSSTemplateToLocalCSS()
     *
     * @param string $template
     * @param hex160 $ID
     *
     * @return string
     */
    static function stylesTemplateToStylesCSS($template, $ID) {
        $keyword = 'view';
        $escape = "\\\\{$keyword}";
        $hash = sha1($escape);
        $selector = ".T{$ID}";
        $css = $template;
        $css = preg_replace("/{$escape}/", $hash, $css);
        $css = preg_replace("/\\.?{$keyword}/", $selector, $css);
        $css = preg_replace("/{$hash}/", $keyword, $css);

        if (preg_match('/<\\/style *>/', $css, $matches)) {
            throw new RuntimeException("The styles template specified for the \$template argument contains the string \"{$matches[0]}\" which is not allowed for security reasons.");
        }

        return $css;
    }

    /**
     * This function replaces the strings "view" or ".view" with the CSS
     * class name for the theme. The string "\view" will not be replaced.
     *
     * @return string
     */
    private static function templateToStyles($template, $ID, $title, $className) {
        $styles = CBTheme::stylesTemplateToStylesCSS($template, $ID);
        $styles = "{$styles}\n\n/**\n * Styles for the \"{$title}\" {$className}\n */\n";
        return $styles;
    }

    /**
     * Call this function while rendering to make sure all of the theme
     * resources will be loaded.
     *
     * @param hex160? $ID
     *
     * @return null
     */
    static function useThemeWithID($ID) {
        if (empty($ID)) { return; }

        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($ID));

        $model = CBModelCache::fetchModelByID($ID);

        if (!empty($model->classNameForTheme)) {
            CBHTMLOutput::requireClassName($model->classNameForTheme);
        }
    }
}
