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

        if (!empty($layoutModel->localCSS)) {
            CBHTMLOutput::addCSS($layoutModel->localCSS);
        }

        $customLayoutClassName = CBModel::value($layoutModel, 'customLayoutClassName', '');
        $customLayoutProperties = CBModel::valueAsObject($layoutModel, 'customLayoutProperties');

        CBPageLayout::renderPageHeader($customLayoutClassName, $customLayoutProperties);

        ?>

        <main class="CBPageLayout <?= $CSSClassNames ?>">
            <?php

                if (!empty($layoutModel->isArticle)) { echo "<article>"; }

                if (is_callable($function = "{$customLayoutClassName}::renderPageContent")) {
                    call_user_func($function, $customLayoutProperties);
                } else {
                    $renderContentCallback();
                }

                if (!empty($layoutModel->isArticle)) { echo "</article>"; }

            ?>
        </main>

        <?php

        CBPageLayout::renderPageFooter($customLayoutClassName, $customLayoutProperties);
    }

    /**
     * Call this function without parameters to render the default page footer.
     *
     * @param string? $className
     *
     *      This class name is usually the customLayoutClassName of the layout
     *      model.
     *
     * @param object? $properties
     *
     *      These properties are usually the customLayoutProperties of the
     *      layout model.
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
            CBView::render((object)[
                'className' => 'CBDefaultPageFooterView',
            ]);
        }
    }

    /**
     * Call this function without parameters to render the default page header.
     *
     * @param string? $className
     *
     *      This class name is usually the customLayoutClassName of the layout
     *      model.
     *
     * @param object? $properties
     *
     *      These properties are usually the customLayoutProperties of the
     *      layout model.
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
            CBView::render((object)[
                'className' => 'CBDefaultPageHeaderView',
            ]);
        }
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
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
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'CSSClassNames' => CBModel::value($spec, 'CSSClassNames', [], 'CBConvert::stringToCSSClassNames'),
            'customLayoutClassName' => CBModel::value($spec, 'customLayoutClassName', '', 'trim'),
            'customLayoutProperties' => CBModel::value($spec, 'customLayoutProperties', (object)[]),
            'isArticle' => CBModel::value($spec, 'isArticle', false, 'boolval'),
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
        $CSSTemplate = CBModel::value($spec, 'localCSSTemplate', '', 'trim');

        /**
         * Step 4: Generate the view CSS.
         */
        $model->localCSS = CBView::CSSTemplateToCSS($CSSTemplate, $uniqueCSSClassName);

        return $model;
    }
}
