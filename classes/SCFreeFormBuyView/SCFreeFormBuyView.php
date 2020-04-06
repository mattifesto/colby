<?php

final class SCFreeFormBuyView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v146.1.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBUI',
            'CBUIStringEditor',
            'Colby',
            'SCShoppingCart',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $cartItemSpec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $cartItemSpec
    ): stdClass {
        return (object)[];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $viewModel
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        ?>

        <div class="SCFreeFormBuyView CBUI_view CBUI_padding_standard_vertical">
        </div>

        <?php
    }
    /* CBView_render() */

}
