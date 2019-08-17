<?php

final class CBTextView2 {

    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBAjax_convertToCBMessageView(stdClass $spec): stdClass {
        return CBTextView2::convertToCBMessageView($spec);
    }


    /**
     * @return string
     */
    static function CBAjax_convertToCBMessageView_group(): string {
        return 'Administrators';
    }


    /* -- CBInstall interfaces -- -- -- -- -- */

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__,
            (object)[
                'isDeprecated' => true,
            ]
        );
    }
    /* CBInstall_install() */


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */


    /* -- CBModel interfaces -- -- -- -- -- */

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

        if (
            !empty($model->isCustom) ||
            cb_array_any("CBCSS::isCustom", $CSSClassNames)
        ) {
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
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */


    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $model = (object)[
            'contentAsCommonMark' => CBModel::valueToString(
                $spec,
                'contentAsCommonMark'
            ),
            'CSSClassNames' => CBModel::valueToNames($spec, 'CSSClassNames'),
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
        $localCSSTemplate = trim(
            CBModel::valueToString($spec, 'localCSSTemplate')
        );

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBHex160::random();
            $model->CSSClassNames[] = $localCSSClassName;
            $model->localCSS = CBView::localCSSTemplateToLocalCSS(
                $localCSSTemplate,
                'view',
                ".{$localCSSClassName}"
            );
        }

        return $model;
    }
    /* CBModel_build() */


    /* -- functions -- -- -- -- -- */

    /**
     * @param object $originalSpec
     *
     * @return object
     */
    static function convertToCBMessageView(stdClass $originalSpec): stdClass {
        $messageViewSpec = CBModel::clone($originalSpec);

        $messageViewSpec->className = 'CBMessageView';

        $messageViewSpec->markup = CBMessageMarkup::stringToMessage(
            CBModel::valueToString(
                $originalSpec,
                'contentAsCommonMark'
            )
        );

        unset($messageViewSpec->contentAsCommonMark);

        $messageViewSpec->CSSTemplate = CBModel::valueToString(
            $originalSpec,
            'localCSSTemplate'
        );

        unset($messageViewSpec->localCSSTemplate);

        return $messageViewSpec;
    }
    /* convertToCBMessageView() */
}
