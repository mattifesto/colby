<?php

final class CBDefaultPageHeaderView {

    /**
     * The $item parameter is a CBMenuItem model but only the following
     * properties are used.
     *
     * @param string? $item->textAsHTML
     * @param string? $item->URLAsHTML
     *
     * @return null
     */
    static function renderMenuItem(stdClass $item) {
        $textAsHTML = CBModel::value($item, 'textAsHTML', '');

        ?>

        <li>
            <a href="<?= $item->URLAsHTML ?>"><?= $textAsHTML ?></a>
        </li>

        <?php
    }

    /**
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        ?>

        <header class="CBDefaultPageHeaderView">
            <nav class="CBDarkTheme">
                <ul>
                    <?php

                    CBDefaultPageHeaderView::renderMenuItem((object)[
                        'textAsHTML' => cbhtml(CBSitePreferences::siteName()),
                        'URLAsHTML' => '/'
                    ]);

                    $menu = CBModels::fetchModelByID(CBMainMenu::ID);

                    array_walk($menu->items, 'CBDefaultPageHeaderView::renderMenuItem');

                    ?>

                </ul>
            </nav>
        </header>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}
