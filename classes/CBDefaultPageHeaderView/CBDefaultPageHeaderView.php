<?php

final class CBDefaultPageHeaderView {

    /**
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        ?>

        <header class="CBDefaultPageHeaderView">
            <?php

            CBView::renderModelAsHTML((object)[
                'className' => 'CBMenuView',
                'menuID' => CBWellKnownMenuForMain::ID(),
                'CSSClassNames' => ['CBDarkTheme'],
            ]);

            ?>
        </header>

        <?php
    }
}
