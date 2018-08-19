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
                'menuIndex' => 0,
            ]);
            */

            ?>
        </header>

        <?php
    }
}
