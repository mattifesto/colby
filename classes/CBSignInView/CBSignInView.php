<?php

final class CBSignInView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v564.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'CBUI',
            'CBUIPanel',
            'CBUIPasswordEditor',
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
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if ($currentUserCBID !== null) {
            return;
        }

        ?>

        <div class="CBSignInView">
        </div>

        <?php
    }
    /* CBView_render() */

}
