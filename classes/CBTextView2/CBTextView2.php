<?php

final class CBTextView2 {

    /**
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        return CBModel::value($model, 'contentAsCommonMark', '', 'trim');
    }

    /**
     * This method can be used outside of the view context to render any HTML
     * formatted content if required.
     *
     * @param string? $model->contentAsHTML
     *
     *      If this is empty, the view will not render.
     *
     * @param [string]? $model->CSSClassNames
     * @param bool? $model->isCustom
     *
     *      @deprecated use "custom" CSS class name
     *
     *      If this is true, the renderer will not include the standard CSS
     *      class names and will only include the class names specified by the
     *      CSSClassNames property. This disables the standard formatting of the
     *      view and allows for fully customized presentation.
     *
     * @param string? $model->localCSS
     *
     *      If this parameter is used, the styles should refer to the element
     *      by a distinct local CSS class name that must also be included in the
     *      $model->CSSClassNames parameter.
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        if (empty($model->contentAsHTML)) {
            echo '<!-- CBTextView2 with no content -->';
            return;
        }

        $CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames');

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        if (!empty($model->isCustom) || cb_array_any("CBCSS::isCustom", $CSSClassNames)) {
            // custom
        } else {
            $CSSClassNames[] = 'CBTextView2_default';
        }

        if (in_array('hero1', $CSSClassNames)) {
            $CSSClassNames[] = 'CBTextView2_hero1';
        }

        $CSSClassNames = cbhtml(implode(' ', $CSSClassNames));

        if (!empty($model->localCSS)) {
            CBHTMLOutput::addCSS($model->localCSS);
        }

        ?>

        <div class="CBTextView2 <?= $CSSClassNames ?>">
            <div class="content">
                <?= CBModel::value($model, 'contentAsHTML', '') ?>
            </div>
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @param string? $spec->contentAsCommonMark
     * @param string? $spec->CSSClassNames
     * @param bool? $spec->isCustom
     * @param string? $spec->localCSSTemplate
     *
     * @return stdClass
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'contentAsCommonMark' => CBModel::value($spec, 'contentAsCommonMark', ''),
            'CSSClassNames' => CBModel::valueAsNames($spec, 'CSSClassNames'),
            'isCustom' => CBModel::value($spec, 'isCustom', false, 'boolval'),
        ];

        // Views with no content will not render, contentAsHTML is left empty
        if (empty(trim($model->contentAsCommonMark))) {
            return $model;
        }

        // contentAsHTML
        $parsedown = new Parsedown();
        $model->contentAsHTML = $parsedown->text($model->contentAsCommonMark);

        // localCSS
        $localCSSTemplate = CBModel::value($spec, 'localCSSTemplate', '', 'trim');

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBHex160::random();
            $model->CSSClassNames[] = $localCSSClassName;
            $model->localCSS = CBView::localCSSTemplateToLocalCSS($localCSSTemplate, 'view', ".{$localCSSClassName}");
        }

        return $model;
    }
}
