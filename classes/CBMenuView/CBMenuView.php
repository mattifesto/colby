<?php

final class CBMenuView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v376.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v376.js', cbsysurl()),
        ];
    }

    /**
     * @param model $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'CSSClassNames' => CBModel::valueToNames($spec, 'CSSClassNames'),
            'menuID' => CBModel::valueAsID($spec, 'menuID'),
            'menuIndex' => CBModel::valueAsInt($spec, 'menuIndex'),
            'selectedItemName' => CBModel::valueToString($spec, 'selectedItemName'),
        ];
    }

    /**
     * @param model $model
     *
     *      {
     *          CSSClassNames: [string]
     *          menu: object
     *
     *              This parameter is used by direct callers that dynamically
     *              construct a menu. It takes higher priority than menuID. This
     *              parameter will not be preserved by CBModel_toModel().
     *
     *          menuID: ID
     *          menuIndex: ?int (optional)
     *
     *              This parameter indicates the level of the menu on the page.
     *              For instance, index 0 means this is a view of the main menu.
     *              Index 1 means it's a view of the second menu, or first
     *              submenu, etc.
     *
     *              When a page specifies a "selectedMenuItemNames" property
     *              value of "products cards" it means the select item name of
     *              the main menu is "products", and the selected item name of
     *              the first submenu is "cards".
     *
     *              If the "selectedItemName" property is not set this property
     *              will be used in conjunction with the page information to
     *              determine the selected menu item name.
     *
     *          selectedItemName: string (optional)
     *      }
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $menu = CBModel::valueAsObject($model, 'menu');

        if (empty($menu)) {
            $menuID = CBModel::valueAsID($model, 'menuID');

            if (empty($menuID)) {
                echo '<!-- CBMenuView: no menu ID -->';
                return;
            }

            $menu = CBModelCache::fetchModelByID($menuID);
        }

        if (empty($menu) || (empty($menu->title) && empty($menu->items))) {
            echo '<!-- CBMenuView: no menu items -->';
            return;
        }

        $selectedItemName = CBModel::valueToString($model, 'selectedItemName');

        if (empty($selectedItemName)) {
            $menuIndex = CBModel::valueAsInt($model, 'menuIndex');

            if ($menuIndex !== null) {
                $selectedMenuItemNames = CBModel::valueToArray(
                    CBHTMLOutput::pageInformation(),
                    'selectedMenuItemNames'
                );

                $selectedItemName = $selectedMenuItemNames[$menuIndex] ?? '';
            }
        }

        $CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames');

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

        $titleAsHTML = cbhtml(CBModel::valueToString($menu, 'title'));
        $titleURI = CBModel::valueToString($menu, 'titleURI');

        if ($titleURI === '') {
            $titleHREFAttribute = '';
        } else {
            $titleHREFAttribute = 'href="' . cbhtml($titleURI) . '"';
        }

        /**
         * HTML Structure
         *
         *      <nav class="CBMenuView">
         *          <div class="CBMenuView_header">
         *              <div class="left">
         *              <div class="center">
         *              <div class="right">
         *          </div>
         *          <div class="CBMenuView_items">
         *              <div class="CBMenuView_container">
         *                  <div class="CBMenuView_containerTitle">
         *                  <ul>
         *                      <li class="CBMenuView_listItemTitle title">
         *                      <li>
         *                      <li>
         *                  </ul>
         *              </div>
         *          </div>
         *      </nav>
         *
         * The CBMenuView_header and CBMenuView_items elements are intended to
         * have 100% width.
         */

        ?>

        <nav class="CBMenuView <?= $CSSClassNames ?>">
            <div class="CBMenuView_header menu_header">
                <div class="left"><a class="title" <?= $titleHREFAttribute ?>><?= $titleAsHTML ?></a></div>
                <div class="center"><a class="title" <?= $titleHREFAttribute ?>><?= $titleAsHTML ?></a></div>
                <div class="right"></div>
            </div>
            <div class="CBMenuView_items menu_items">
                <div class="CBMenuView_container container">
                    <?php

                    if (!empty($menu->title)) {
                        ?>

                        <div class="CBMenuView_containerTitle title"><a <?= $titleHREFAttribute ?>><?= $titleAsHTML ?></a></div>

                        <?php
                    }

                    ?>

                    <ul>
                        <li class="CBMenuView_listItemTitle title"><a <?= $titleHREFAttribute ?>><span><?= $titleAsHTML ?></span></a></li>

                        <?php

                        $items = CBModel::valueToArray($menu, 'items');

                        array_walk($items, function ($item) use ($selectedItemName) {
                            $name = CBModel::valueToString($item, 'name');
                            $textAsHTML = cbhtml(CBModel::valueToString($item, 'text'));
                            $URLAsHTML = cbhtml(CBModel::valueToString($item, 'URL'));

                            if (!empty($selectedItemName) && $name === $selectedItemName) {
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
}
