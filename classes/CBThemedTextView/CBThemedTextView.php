<?php

final class CBThemedTextView {

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
    public static function renderModelAsHTML(stdClass $model) {
        if ($model->themeID) {
            CBHTMLOutput::addCSSURL(CBDataStore::toURL([
                'ID'        => $model->themeID,
                'filename'  => 'CBThemedTextViewTheme.css'
            ]));
        }

        if ($model->themeID) {
            $class = "CBThemedTextView T{$model->themeID}";
        } else {
            $class = "CBThemedTextView";
        }
        if ($model->URLAsHTML) {
            $open   = "<a href=\"{$model->URLAsHTML}\" class=\"{$class}\">";
            $close  = '</a>';
        } else {
            $open   = "<section class=\"{$class}\">";
            $close  = '</section>';
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
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBView::modelWithClassName(__CLASS__);
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
     * @return {string}
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
