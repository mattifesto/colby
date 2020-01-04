<?php

final class CBSignOutView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
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

        <div class="CBUI_container1">
            <a
                class="CBUI_button1"
                href="<?= ColbyUser::logoutURL() ?>"
            >
                Sign Out
            </a>
        </div>

        <?php
    }
    /* CBView_render() */

}
