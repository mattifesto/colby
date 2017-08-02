<?php

final class CBMenuView {

    /**
     * @param object $model
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        $menuID = CBModel::value($model, 'menuID');

        if (empty($menuID)) {
            echo '<!-- CBMenuView: no menu ID -->';
            return;
        }

        $menu = CBModels::fetchModelByID($menuID);

        if (empty($menu) || empty($menu->items)) {
            echo '<!-- CBMenuView: no menu items -->';
            return;
        }

        $selectedItemName = CBModel::value($model, 'selectedItemName');
        $CSSClassNames = CBModel::valueAsArray($model, 'CSSClassNames');

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        if (in_array('submenu1', $CSSClassNames)) {
            $CSSClassNames[] = 'CBMenuView_submenu1';
        } else if (!in_array('custom', $CSSClassNames)) {
            $CSSClassNames[] = 'CBMenuView_default';
        }

        if (count($menu->items) < 5) {
            $CSSClassNames[] = 'few';
        }

        $CSSClassNames = implode(' ', $CSSClassNames);

        $titleAsHTML = cbhtml($menu->title);
        $titleURIAsHTML = CBModel::value($menu, 'titleURI', '', 'cbhtml');

        /**
         * HTML Structure
         *
         *      .CBMenuView
         *          .header
         *              .left, .center, .right
         *          .menu
         *              .container
         *                  .title, ul
         *
         *  The .header and .menu elements are intended to have 100% width.
         */

        ?>

        <nav class="CBMenuView <?= $CSSClassNames ?>">
            <div class="menu_header">
                <div class="left"><a class="title" href="<?= $titleURIAsHTML ?>"><?= $titleAsHTML ?></a></div>
                <div class="center"></div>
                <div class="right"></div>
            </div>
            <div class="menu_items">
                <div class="container">
                    <?php

                    if (!empty($menu->title)) {
                        ?>

                        <div class="title"><a href="<?= $titleURIAsHTML ?>"><?= $titleAsHTML ?></a></div>

                        <?php
                    }

                    ?>

                    <ul>
                        <?php

                        $items = CBModel::valueAsArray($menu, 'items');

                        array_walk($items, function ($item) use ($selectedItemName) {
                            $name = CBModel::value($item, 'name', '');
                            $textAsHTML = CBModel::value($item, 'text', '');
                            $URLAsHTML = CBModel::value($item, 'URL', '', 'cbhtml');

                            if ($name === $selectedItemName) {
                                ?>

                                <li class="selected"><a href="<?= $URLAsHTML ?>"><span><?= $textAsHTML ?></span></a></li>

                                <?php
                            } else {
                                ?>

                                <li><a href="<?= $URLAsHTML ?>"><span><?= $textAsHTML ?></span></a></li>

                                <?php
                            }
                        });

                        ?>
                    </ul>
                </div>
            </div>
        </nav>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'CSSClassNames' => CBModel::valueAsNames($spec, 'CSSClassNames'),
            'menuID' => CBModel::value($spec, 'menuID'),
            'selectedItemName' => CBModel::value($spec, 'selectedItemName'),
        ];
    }
}
