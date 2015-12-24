<?php

final class CBThemedTextView {

    const standardPageHeaderThemeID = '2a5eb6c836914ef8f33b15f0853ac61df554505e';

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [ CBThemedTextView::URL('CBThemedTextViewEditor.css') ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBResponsiveEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBThemedTextView::URL('CBThemedTextViewEditorFactory.js')
        ];
    }

    /**
     * @return {stdClass}
     */
    public static function fetchThemesForAjax() {
        $response   = new CBAjaxResponse();
        $SQL        = <<<EOT

            SELECT      `v`.`modelAsJSON`
            FROM        `CBModels` AS `m`
            JOIN        `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE       `m`.`className` = 'CBThemedTextViewTheme'
            ORDER BY    `m`.`created`

EOT;

        $models                     = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
        $themes                     = array_map(function($model) {
            return (object)['value' => $model->ID, 'textContent' => $model->title];
        }, $models);
        $response->themes           = $themes;
        $response->wasSuccessful    = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchThemesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function install() {

        // Ensure the standard page header theme exists.

        $spec = CBModels::fetchSpecByID(CBThemedTextView::standardPageHeaderThemeID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName('CBThemedTextViewTheme', [
                'ID' => CBThemedTextView::standardPageHeaderThemeID
            ]);
            $spec->title = 'Standard Page Header';

            CBModels::save([$spec]);
        }
    }

    /**
     * @param {stdClass} $model
     *
     * @return {string}
     */
    public static function modelToSearchText(stdClass $model) {
        return "{$model->title} {$model->contentAsMarkaround}";
    }

    /**
     * @param string? $model->contentAsHTML
     * @param hex160? $model->themeID
     * @param string? $model->titleAsHTML
     * @param string? $model->URLAsHTML
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if (empty($model->themeID)) {
            $class = "CBThemedTextView NoTheme";
        } else {
            CBHTMLOutput::addCSSURL(CBDataStore::toURL([
                'ID'        => $model->themeID,
                'filename'  => 'CBThemedTextViewTheme.css'
            ]));

            $class = "CBThemedTextView T{$model->themeID}";
        }

        if (empty($model->URLAsHTML)) {
            $open   = "<section class=\"{$class}\">";
            $close  = '</section>';
        } else {
            $open   = "<a href=\"{$model->URLAsHTML}\" class=\"{$class}\">";
            $close  = '</a>';
        }

        if (empty($model->titleAsHTML)) {
            $title = '';
        } else {
            $title = "<h1>{$model->titleAsHTML}</h1>";
        }

        if (empty($model->contentAsHTML)) {
            $content = '';
        } else {
            $content = "<div>{$model->contentAsHTML}</div>";
        }

        echo $open, $title, $content, $close;
    }

    /**
     * @param {stdClass} $spec
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? trim($spec->contentAsMarkaround) : '';
        $model->contentAsHTML = ColbyConvert::markaroundToHTML($model->contentAsMarkaround);
        $model->themeID = isset($spec->themeID) ? $spec->themeID : false;
        $model->titleAsMarkaround = isset($spec->titleAsMarkaround) ? trim($spec->titleAsMarkaround) : '';
        $model->title = CBMarkaround::paragraphToText($model->titleAsMarkaround);
        $model->titleAsHTML = CBMarkaround::paragraphToHTML($model->titleAsMarkaround);
        $model->URL = isset($spec->URL) ? trim($spec->URL) : '';
        $model->URLAsHTML = ColbyConvert::textToHTML($model->URL);

        return $model;
    }

    /**
     * @param {string} $filename
     *
     * @return {string}
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
