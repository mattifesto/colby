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
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        $ID         = 'ID' . CBHex160::random();
        $styles     = [];
        $styles[]   = "#{$ID} h1 { text-align: {$model->titleAlignment}; }";
        $styles[]   = "#{$ID} div { text-align: {$model->contentAlignment}; }";

        if ($model->contentColor) {
            $styles[] = "#{$ID} div { color: {$model->contentColor}; }";
        }

        if ($model->height !== false) {
            $styles[] = "#{$ID} { height: {$model->height}px; }";
        }

        if ($model->titleColor) {
            $styles[] = "#{$ID} h1 { color: {$model->titleColor}; }";
        }

        if ($model->verticalAlignment === 'center') {
            $styles[] = "#{$ID} { justify-content: center; -webkit-justify-content: center; }";
        } else if ($model->verticalAlignment === 'bottom') {
            $styles[] = "#{$ID} { justify-content: flex-end; -webkit-justify-content: flex-end; }";
        }

        if ($model->width !== false) {
            $styles[] = "#{$ID} { width: {$model->width}px; }";
        }

        $themeClass = $model->themeID ? "T{$model->themeID}" : 'NoTheme';
        $properties = "class=\"CBTextBoxView {$themeClass}\" id=\"{$ID}\"";

        if ($model->URLAsHTML) {
            $open   = "<a href=\"{$model->URLAsHTML}\" {$properties}>";
            $close  = '</a>';
        } else {
            $open   = "<section {$properties}>";
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

        CBHTMLOutput::addCSSURL(CBTextBoxView::URL("CBTextBoxView.css"));

        if ($model->themeID) {
            CBHTMLOutput::addCSSURL(CBDataStore::toURL([
                'ID'        => $model->themeID,
                'filename'  => 'theme.css'
            ]));
        }

        ?>

        <?= $open ?>
            <style scoped><?= implode("\n", $styles) ?></style>
            <?= $title ?>
            <?= $content ?>
        <?= $close ?>

        <?php
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->contentAlignment    = "left";
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? trim($spec->contentAsMarkaround) : '';
        $model->contentAsHTML       = ColbyConvert::markaroundToHTML($model->contentAsMarkaround);
        $model->contentColor        = isset($spec->contentColor) ? trim($spec->contentColor) : '';
        $model->height              = CBTextBoxView::propertyToNumber($spec, 'height');
        $model->themeID             = isset($spec->themeID) ? $spec->themeID : false;
        $model->titleAlignment      = "left";
        $model->titleAsMarkaround   = isset($spec->titleAsMarkaround) ? trim($spec->titleAsMarkaround) : '';
        $model->titleAsHTML         = CBMarkaround::paragraphToHTML($model->titleAsMarkaround);
        $model->titleColor          = isset($spec->titleColor) ? trim($spec->titleColor) : '';
        $model->URL                 = isset($spec->URL) ? trim($spec->URL) : '';
        $model->URLAsHTML           = ColbyConvert::textToHTML($model->URL);
        $model->verticalAlignment   = "top";
        $model->width               = CBTextBoxView::propertyToNumber($spec, 'width');

        if (isset($spec->verticalAlignment)) {
            switch ($spec->verticalAlignment) {
                case "center":
                case "bottom":
                    $model->verticalAlignment = $spec->verticalAlignment;
                    break;
                default:
                    // default value is set above
                    break;
            }
        }

        if (isset($spec->titleAlignment)) {
            switch ($spec->titleAlignment) {
                case "center":
                case "right":
                case "justify":
                    $model->titleAlignment = $spec->titleAlignment;
                default:
                    // default value is set above
                    break;
            }
        }

        if (isset($spec->contentAlignment)) {
            switch ($spec->contentAlignment) {
                case "center":
                case "right":
                case "justify":
                    $model->contentAlignment = $spec->contentAlignment;
                default:
                    // default value is set above
                    break;
            }
        }

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBTextBoxView/{$filename}";
    }
}
