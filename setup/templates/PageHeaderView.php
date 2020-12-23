<?php

final class PREFIXPageHeaderView {

    /* -- CBView interfaces -- */



    /**
     * @param object $model
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $model
    ): void {
        $selectedMenuItemNames = CBHTMLOutput::getSelectedMenuItemNamesArray();

        $selectedMainMenuItemName = (
            count($selectedMenuItemNames) > 0 ?
            $selectedMenuItemNames[0] :
            ''
        );

        ?>

        <header class="PREFIXPageHeaderView CBDarkTheme">
            <?php

            CBView::render(
                (object)[
                    'className' => 'CBMenuView',
                    'menuID' => PREFIXMenu_main::ID(),
                    'selectedItemName' => $selectedMainMenuItemName,
                ]
            );

            ?>
        </header>

        <?php
    }
    /* CBView_render() */

}
