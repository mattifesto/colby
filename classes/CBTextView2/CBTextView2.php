<?php

final class CBTextView2 {

    /**
     * @return string
     */
    static function modelToSearchText(stdClass $model) {
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
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        if (empty($model->contentAsHTML)) {
            echo '<!-- CBTextView2 with no content -->';
            return;
        }

        if (!empty($model->CSSClassNames) && is_array($model->CSSClassNames)) {
            if (empty($model->isCustom)) {
                $standardCSSClassNames = ['CBTextView2StandardLayout'];
                $CSSClassNames = array_unique(array_merge($standardCSSClassNames, $model->CSSClassNames));
            } else {
                $CSSClassNames = $model->CSSClassNames;
            }

            array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

            $classes = cbhtml(implode(' ', $CSSClassNames));
        } else {
            $classes = "";
        }

        ?>

        <div class="CBTextView2 <?= $classes ?>">
            <?php if (!empty($model->localCSS)) { ?>
                <style><?= $model->localCSS ?></style>
            <?php } ?>
            <div>
                <?= CBModel::value($model, 'contentAsHTML', '') ?>
            </div>
        </div>

        <?php
    }

    /**
     * @param string? $spec->contentAsCommonMark
     * @param string? $spec->CSSClassNames
     * @param bool? $spec->isCustom
     * @param string? $spec->localCSSTemplate
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'contentAsCommonMark' => CBModel::value($spec, 'contentAsCommonMark', ''),
            'isCustom' => CBModel::value($spec, 'isCustom', false, 'boolval'),
        ];

        // Views with no content will not render, contentAsHTML is left empty
        if (empty(trim($model->contentAsCommonMark))) {
            return $model;
        }

        // contentAsHTML
        $parsedown = new Parsedown();
        $model->contentAsHTML = $parsedown->text($model->contentAsCommonMark);

        // CSSClassNames
        $CSSClassNames = CBModel::value($spec, 'CSSClassNames', '');
        $CSSClassNames = preg_split('/[\s,]+/', $CSSClassNames, null, PREG_SPLIT_NO_EMPTY);

        if ($CSSClassNames === false) {
            throw new RuntimeException("preg_split() returned false");
        }

        $model->CSSClassNames = $CSSClassNames;

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
