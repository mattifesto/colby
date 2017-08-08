<?php

final class CBTextBoxViewTheme {

    /**
     * @return stdClass
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Text Box View Themes',
            'singularTitle' => 'Text Box View Theme'
        ]);
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
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $template = CBModel::value($spec, 'styles', '');
        $ID = CBModel::valueAsID($spec, 'ID');
        $title = CBModel::value($spec, 'title', '');

        return (object)[
            'className' => __CLASS__,
            'styles' => CBTextBoxViewTheme::templateToStyles($template, $ID, $title),
        ];
    }

    /**
     * This function replaces the strings "textbox" or ".textbox" with the CSS
     * class name for the CBTextBoxViewTheme. The string "\textbox" will not be
     * replaced.
     *
     * @return string
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
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
