<?php

final class CBDefaultPageHeaderView {

    /**
     * @return null
     */
    static function CBView_render(stdClass $model) {
        ?>

        <header class="CBDefaultPageHeaderView CBDarkTheme">
            <?php

            CBView::renderModelAsHTML((object)[
                'className' => 'CBMenuView',
                'menuID' => CBWellKnownMenuForMain::ID(),
            ]);

            ?>
        </header>

        <?php
    }
}
