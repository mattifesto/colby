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
        $customLayoutProperties = CBModel::valueToObject($layoutModel, 'customLayoutProperties');

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
            'CSSClassNames' => CBConvert::stringToCSSClassNames(CBConvert::valueToString(CBModel::value($spec, 'CSSClassNames'))),
            'customLayoutClassName' => trim(CBModel::value($spec, 'customLayoutClassName')),
            'customLayoutProperties' => CBConvert::valueToObject(CBModel::value($spec, 'customLayoutProperties')),
            'isArticle' => (bool)CBModel::value($spec, 'isArticle'),
        ];

        /**
         * Local CSS Template
         * See CBView::CSSTemplateToCSS for documentation.
         */

         $CSSTemplate = trim(CBConvert::valueToString(CBModel::value($spec, 'localCSSTemplate')));

         if ($CSSTemplate !== '') {
             $uniqueCSSClassName = 'ID_' . CBHex160::random();
             $model->CSSClassNames[] = $uniqueCSSClassName;
             $model->localCSS = CBView::CSSTemplateToCSS($CSSTemplate, $uniqueCSSClassName);
         }

        return $model;
    }
}
