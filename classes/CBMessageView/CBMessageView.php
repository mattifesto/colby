<?php

final class CBMessageView {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
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
     * @param object $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'html' => CBMessageMarkup::markupToHTML(
                CBModel::valueToString($spec, 'markup')
            ),

            'markup' => CBModel::valueToString($spec, 'markup'),

            'CSSClassNames' => CBModel::valueToNames($spec, 'CSSClassNames'),
        ];

        /**
         * Using local styles.
         *
         * Step 1: Create a globally unique CSS class name for the view.
         */
        $uniqueCSSClassName = 'ID_' . CBID::generateRandomCBID();

        /**
         * Step 2: Add the unique class name to the view's CSS class names.
         */
        $model->CSSClassNames[] = $uniqueCSSClassName;

        /**
         * Step 3: Get the CSS template.
         */
        $CSSTemplate = trim(
            CBModel::valueToString($spec, 'CSSTemplate')
        );

        /**
         * Step 4: Generate the view CSS.
         */
        $model->CSS = CBView::CSSTemplateToCSS(
            $CSSTemplate,
            $uniqueCSSClassName
        );

        return $model;
    }
    /* CBModel_build() */


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
     * @param model $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        if (empty($model->html)) {
            echo '<!-- CBMessageView with no content. -->';
            return;
        }

        $CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames');

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
