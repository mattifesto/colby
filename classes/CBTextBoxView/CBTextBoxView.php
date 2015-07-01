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
        $ID     = 'ID' . CBHex160::random();
        $styles = array_map(function($style) use ($ID) {
            return "#{$ID} {$style}";
        }, $model->styles);

        $styles[] = "#{$ID} section { display: flex; display: -webkit-flex; flex-direction: column; -webkit-flex-direction: column; }";
        $styles[] = "#{$ID} h1 { text-align: {$model->titleAlignment}; }";
        $styles[] = "#{$ID} div { text-align: {$model->contentAlignment}; }";

        if ($model->height !== false) {
            $styles[] = "#{$ID} section { height: {$model->height}px; }";
        }

        if ($model->width !== false) {
            $styles[] = "#{$ID} section { width: {$model->width}px; }";
        }

        if ($model->verticalAlignment === 'center') {
            $styles[] = "#{$ID} section { justify-content: center; -webkit-justify-content: center; }";
        } else if ($model->verticalAlignment === 'bottom') {
            $styles[] = "#{$ID} section { justify-content: flex-end; -webkit-justify-content: flex-end; }";
        }

        array_walk($model->URLsForCSSAsHTML, function($URL) {
            CBHTMLOutput::addCSSURL($URL);
        });

        if ($model->URLAsHTML) {
            $openAnchor     = "<a href=\"{$model->URLAsHTML}\">";
            $closeAnchor    = '</a>';
        } else {
            $openAnchor = $closeAnchor = '';
        }

        ?>

        <div class="CBTextBoxView" id="<?= $ID ?>">
            <style scoped><?= implode("\n", $styles) ?></style>
            <?= $openAnchor ?>
                <section>
                    <h1><?= $model->titleAsHTML ?></h1>
                    <div><?= $model->contentAsHTML ?></div>
                </section>
            <?= $closeAnchor ?>
        </div>

        <?php
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->contentAlignment    = "left";
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? $spec->contentAsMarkaround : '';
        $model->contentAsHTML       = CBMarkaround::textToHTML(['text' => $model->contentAsMarkaround]);
        $model->height              = CBTextBoxView::propertyToNumber($spec, 'height');
        $model->styles              = [];
        $model->titleAlignment      = "left";
        $model->titleAsMarkaround   = isset($spec->titleAsMarkaround) ? $spec->titleAsMarkaround : '';
        $model->titleAsHTML         = CBMarkaround::paragraphToHTML($model->titleAsMarkaround);
        $model->URL                 = isset($spec->URL) ? trim($spec->URL) : '';
        $model->URLAsHTML           = ColbyConvert::textToHTML($model->URL);
        $model->URLsForCSSAsHTML    = [];
        $model->verticalAlignment   = "top";
        $model->width               = CBTextBoxView::propertyToNumber($spec, 'width');

        if (isset($spec->themeID)) {
            $theme = CBModels::fetchModelByID($spec->themeID);

            if ($theme) {
                $model->styles              = $theme->styles;
                $model->URLsForCSSAsHTML    = $theme->URLsForCSSAsHTML;
            }
        }

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
