<?php

final class CBSignOutView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v584.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'CBUI',
            'CBUser',
            'Colby',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $viewSpec
     *
     * @return void
     */
    static function CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        return (object)[];
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
        ?>

        <div class="CBSignOutView">
        </div>

        <?php
    }
    /* CBView_render() */

}
