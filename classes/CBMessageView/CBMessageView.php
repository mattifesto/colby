<?php

final class CBMessageView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'html' => CBModel::value($spec, 'markup', '', 'CBMessageMarkup::markupToHTML'),
            'markup' => CBModel::value($spec, 'markup', '', 'trim'),
            'CSSClassNames' => CBModel::value($spec, 'CSSClassNames', [], 'CBConvert::stringToCSSClassNames'),
        ];

        /**
         * Using local styles.
         *
         * Step 1: Create a globally unique CSS class name for the view.
         */
        $uniqueCSSClassName = 'ID_' . CBHex160::random();

        /**
         * Step 2: Add the unique class name to the view's CSS class names.
         */
        $model->CSSClassNames[] = $uniqueCSSClassName;

        /**
         * Step 3: Get the CSS template.
         */
        $CSSTemplate = CBModel::value($spec, 'CSSTemplate', '', 'trim');

        /**
         * Step 4: Generate the view CSS.
         */
        $model->CSS = CBView::CSSTemplateToCSS($CSSTemplate, $uniqueCSSClassName);

        return $model;
    }

    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        if (isset($model->markup)) {
            return CBMessageMarkup::markupToText($model->markup);
        } else {
            return '';
        }
    }

    /**
     * @param object $model
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        if (empty($model->html)) {
            echo '<!-- CBMessageView with no content. -->';
            return;
        }

        $CSSClassNames = CBModel::valueAsArray($model, 'CSSClassNames');

        if (!in_array('custom', $CSSClassNames)) {
            $CSSClassNames[] = 'CBMessageView_default';
            $CSSClassNames[] = 'CBContentStyleSheet';
        }

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        if (!empty($model->CSS)) {
            CBHTMLOutput::addCSS($model->CSS);
        }

        ?>

        <div class="CBMessageView <?= implode(' ', $CSSClassNames) ?>">
            <div class="content">
                <?= CBModel::value($model, 'html', '') ?>
            </div>
        </div>

        <?php
    }
}
