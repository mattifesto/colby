<?php

final class CBThemedTextViewEditor {

    /**
     * @return stdClass
     */
    public static function fetchThemesForAjax() {
        $response = new CBAjaxResponse();
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
        $response->themes = $themes;
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function fetchThemesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBResponsiveEditor'];
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
        return [CBThemedTextViewEditor::URL('CBThemedTextViewEditorFactory.js')];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
