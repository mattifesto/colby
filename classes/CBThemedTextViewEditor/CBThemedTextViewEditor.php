<?php

final class CBThemedTextViewEditor {

    /**
     * @return stdClass
     */
    public static function fetchThemes() {
        $SQL = <<<EOT

            SELECT      `v`.`modelAsJSON`
            FROM        `CBModels` AS `m`
            JOIN        `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE       `m`.`className` = 'CBTheme'
            ORDER BY    `m`.`created`

EOT;

        $models = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
        $models = array_values(array_filter($models, function ($model) {
            return $model->classNameForKind === "CBTextView";
        }));
        $themes = array_map(function($model) {
            return (object)['value' => $model->ID, 'textContent' => $model->title];
        }, $models);

        return $themes;
    }

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBThemedTextViewEditor::URL('CBThemedTextViewEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBThemedTextViewEditor::URL('CBThemedTextViewEditor.js'),
        ];
    }

    /**
     * @return [[string, mixed]]
     */
    public static function requiredJavaScriptVariables() {
        return [
            ['CBThemedTextViewThemes', CBThemedTextViewEditor::fetchThemes()]
        ];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
