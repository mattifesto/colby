<?php

final class CBPageLayout {

    /**
     * @return null
     */
    static function render(stdClass $layoutModel, callable $renderContentCallback) {

        /* CSS class names */

        $CSSClassNames = CBModel::valueAsArray($layoutModel, 'CSSClassNames');

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        $CSSClassNames = cbhtml(implode(' ', $CSSClassNames));

        /* local CSS */

        if (empty($layoutModel->localCSS)) {
            $localCSS = '';
        } else {
            $localCSS = "<style>{$layoutModel->localCSS}</style>";
        }

        CBPageLayout::renderPageHeader($layoutModel->customLayoutClassName, $layoutModel->customLayoutProperties);

        ?>

        <main class="CBPageLayout <?= $CSSClassNames ?>">
            <?= $localCSS ?>
            <?php

                if (!empty($layoutModel->isArticle)) { echo "<article>"; }

                if (is_callable($function = "{$layoutModel->customLayoutClassName}::renderPageContent")) {
                    call_user_func($function, $layoutModel->customLayoutProperties);
                } else {
                    $renderContentCallback();
                }

                if (!empty($layoutModel->isArticle)) { echo "</article>"; }

            ?>
        </main>

        <?php

        CBPageLayout::renderPageFooter($layoutModel->customLayoutClassName, $layoutModel->customLayoutProperties);
    }

    /**
     * Call this function without parameters to render the default page footer.
     *
     * @param string? $className
     * @param stdClass? $properties
     *
     * @return null
     */
    static function renderPageFooter($className = '', stdClass $properties = null) {
        if ($properties === null) { $properties = (object)[]; }

        if (is_callable($function = "{$className}::renderPageFooter")) {
            call_user_func($function, $properties);
        } else if (is_callable($function = 'CBPageHelpers::renderDefaultPageFooter')) {
            call_user_func($function, $properties);
        } else {
            CBView::renderModelAsHTML((object)[
                'className' => 'CBDefaultPageFooterView',
            ]);
        }
    }

    /**
     * Call this function without parameters to render the default page header.
     *
     * @param string? $className
     * @param stdClass? $properties
     *
     * @return null
     */
    static function renderPageHeader($className = '', stdClass $properties = null) {
        if ($properties === null) { $properties = (object)[]; }

        if (is_callable($function = "{$className}::renderPageHeader")) {
            call_user_func($function, $properties);
        } else if (is_callable($function = 'CBPageHelpers::renderDefaultPageHeader')) {
            call_user_func($function, $properties);
        } else {
            CBView::renderModelAsHTML((object)[
                'className' => 'CBDefaultPageHeaderView',
            ]);
        }
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param string? $spec->CSSClassNames
     * @param string? $spec->customLayoutClassName
     * @param stdClass? $spec->customLayoutProperties
     * @param bool? $spec->isArticle
     * @param string? $spec->localCSSTemplate
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'customLayoutClassName' => CBModel::value($spec, 'customLayoutClassName', '', 'trim'),
            'customLayoutProperties' => CBModel::value($spec, 'customLayoutProperties', (object)[]),
            'isArticle' => CBModel::value($spec, 'isArticle', false, 'boolval'),
        ];

        // CSS class names

        $CSSClassNames = CBModel::value($spec, 'CSSClassNames', '');
        $CSSClassNames = preg_split('/[\s,]+/', $CSSClassNames, null, PREG_SPLIT_NO_EMPTY);

        if ($CSSClassNames === false) {
            throw new RuntimeException("preg_split() returned false");
        }

        $model->CSSClassNames = $CSSClassNames;

        // local CSS

        $localCSSTemplate = CBModel::value($spec, 'localCSSTemplate', '', 'trim');

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBHex160::random();
            $model->CSSClassNames[] = $localCSSClassName;
            $model->localCSS = CBView::localCSSTemplateToLocalCSS($localCSSTemplate, 'view', ".{$localCSSClassName}");
        }

        return $model;
    }
}
