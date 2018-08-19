<?php

/**
 * @deprecated 2018.08.19
 *
 *      This class should be replaced with a site specific page header view.
 *      This can be deleted once that transition is confirmed.
 */
final class CBDefaultPageHeaderView {

    /**
     * @return null
     */
    static function CBView_render(stdClass $model) {
        $selectedMainMenuItemName = CBModel::value(CBHTMLOutput::pageInformation(), 'selectedMainMenuItemName');

        ?>

        <header class="CBDefaultPageHeaderView CBDarkTheme">
            <?php

            /**
             * CBWellKnownMenuForMain has been removed.
             */

            /*
            CBView::render((object)[
                'className' => 'CBMenuView',
                'menuID' => CBWellKnownMenuForMain::ID(),
                'selectedItemName' => $selectedMainMenuItemName,
            ]);
            */

            ?>
        </header>

        <?php
    }
}
