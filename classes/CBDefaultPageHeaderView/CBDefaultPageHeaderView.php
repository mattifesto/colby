<?php

final class CBDefaultPageHeaderView {

    /**
     * @return null
     */
    static function CBView_render(stdClass $model) {
        $selectedMainMenuItemName = CBModel::value(CBPageContext::current(), 'selectedMainMenuItemName');

        ?>

        <header class="CBDefaultPageHeaderView CBDarkTheme">
            <?php

            CBView::renderModelAsHTML((object)[
                'className' => 'CBMenuView',
                'menuID' => CBWellKnownMenuForMain::ID(),
                'selectedItemName' => $selectedMainMenuItemName,
            ]);

            ?>
        </header>

        <?php
    }
}
