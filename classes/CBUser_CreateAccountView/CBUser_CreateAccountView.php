<?php

/**
 * @TODO 2020_02_10
 *
 * Consider making this a "local" view similar to SignInView in the sign in
 * handler. This view should only be used on page rendered by the create account
 * handler, not any other pages.
 */
final class
CBUser_CreateAccountView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.48.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBAjax',
            'CBUI',
            'CBUIButton',
            'CBUIPanel',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $viewSpec
     *
     * @return void
     */
    static function CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        return (object)[
            'destinationURL' => CBModel::valueToString(
                $viewSpec,
                'destinationURL'
            ),
        ];
    }



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $destinationURL = CBModel::valueToString(
            $viewModel,
            'destinationURL'
        );

        ?>

        <div
            class="CBUser_CreateAccountView"
            data-destination-u-r-l="<?= cbhtml($destinationURL) ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */

}
