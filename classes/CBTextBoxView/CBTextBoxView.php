<?php

final class CBTextBoxView {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [ CBTextBoxView::URL('CBTextBoxViewEditor.css') ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBTextBoxView::URL('CBTextBoxViewEditorFactory.js')
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
            WHERE       `m`.`className` = 'CBTextBoxViewTheme'
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
     * If the property value exists and is a number the property  value is
     * returned; otherwise false is returned.
     *
     * @return {int} | {float} | {string} | false
     */
    public static function propertyToNumber($object, $property) {
        if (isset($object->{$property})) {
            $value = $object->{$property};

            if (is_int($value) || is_float($value) || is_numeric(trim($value))) {
                return $value;
            }
        }

        return false;
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? $spec->contentAsMarkaround : '';
        $model->contentAsHTML       = CBMarkaround::textToHTML(['text' => $model->contentAsMarkaround]);
        $model->height              = CBTextBoxView::propertyToNumber($spec, 'height');
        $model->styles              = [];
        $model->titleAsMarkaround   = isset($spec->titleAsMarkaround) ? $spec->titleAsMarkaround : '';
        $model->titleAsHTML         = CBMarkaround::paragraphToHTML($model->titleAsMarkaround);
        $model->URLsForCSSAsHTML    = [];
        $model->width               = CBTextBoxView::propertyToNumber($spec, 'width');


        if (isset($spec->themeID)) {
            $theme = CBModels::fetchModelByID($spec->themeID);

            if ($theme) {
                $model->styles              = $theme->styles;
                $model->URLsForCSSAsHTML    = $theme->URLsForCSSAsHTML;
            }
        }

        return $model;
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        $ID     = 'ID' . CBHex160::random();
        $styles = array_map(function($style) use ($ID) {
            return "#{$ID} {$style}";
        }, $model->styles);

        if ($model->height !== false) {
            $styles[] = "#{$ID} { height: {$model->height}px; }";
        }

        if ($model->width !== false) {
            $styles[] = "#{$ID} { width: {$model->width}px; }";
        }

        array_walk($model->URLsForCSSAsHTML, function($URL) {
            CBHTMLOutput::addCSSURL($URL);
        });

        ?>

        <section class="CBTextBoxView" id="<?= $ID ?>">
            <style><?= implode(' ', $styles) ?></style>
            <h1><?= $model->titleAsHTML ?></h1>
            <div><?= $model->contentAsHTML ?></div>
        </section>

        <?php
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBTextBoxView/{$filename}";
    }
}
