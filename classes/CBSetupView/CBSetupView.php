<?php

final class CBSetupView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v612.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
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
     * @return object
     */
    static function CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        return (object)[];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        ?>

        <div class="CBSetupView">
        </div>

        <?php
    }
    /* CBView_render() */

}
