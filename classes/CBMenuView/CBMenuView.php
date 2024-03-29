<?php

final class
CBMenuView
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.70.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v594.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass {
        $menuViewModel = (object)[
            'CSSClassNames' => CBModel::valueToNames(
                $spec,
                'CSSClassNames'
            ),
            'menuIndex' => CBModel::valueAsInt(
                $spec,
                'menuIndex'
            ),
            'selectedItemName' => CBModel::valueToString(
                $spec,
                'selectedItemName'
            ),
        ];

        CBMenuView::setMenuModelCBID(
            $menuViewModel,
            CBMenuView::getMenuModelCBID(
                $spec
            )
        );

        return $menuViewModel;
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     *      {
     *          CSSClassNames: [string]
     *          menu: object
     *
     *              This parameter is used by direct callers that dynamically
     *              construct a menu. It takes higher priority than menuID. This
     *              parameter will not be preserved by CBModel_build().
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
    static function
    CBView_render(
        stdClass $model
    ): void {
        $menu = CBModel::valueAsObject(
            $model,
            'menu'
        );

        if (empty($menu)) {
            $menuID = CBModel::valueAsID(
                $model,
                'menuID'
            );

            if (empty($menuID)) {
                echo '<!-- CBMenuView: no menu ID -->';
                return;
            }

            $menu = CBModelCache::fetchModelByID($menuID);
        }

        if (
            empty($menu) ||
            (
                empty($menu->title) &&
                empty(
                    CBMenu::getMenuItems(
                        $menu
                    )
                )
            )
        ) {
            echo '<!-- CBMenuView: no menu items -->';
            return;
        }

        $selectedItemName = CBModel::valueToString(
            $model,
            'selectedItemName'
        );

        if (empty($selectedItemName)) {
            $menuIndex = CBModel::valueAsInt(
                $model,
                'menuIndex'
            );

            if ($menuIndex !== null) {
                $selectedMenuItemNames = CBModel::valueToArray(
                    CBHTMLOutput::pageInformation(),
                    'selectedMenuItemNames'
                );

                $selectedItemName = $selectedMenuItemNames[$menuIndex] ?? '';
            }
        }

        $CSSClassNames = CBModel::valueToArray(
            $model,
            'CSSClassNames'
        );

        array_walk(
            $CSSClassNames,
            'CBHTMLOutput::requireClassName'
        );

        $builtInClassNames = [
            'custom',
            'list1',
            'CBMenuView_list1',
            'CBMenuView_list2',
            'submenu1',
            'CBMenuView_submenu1',
            'CBMenuView_default',
        ];

        if (
            array_intersect(
                $builtInClassNames,
                $CSSClassNames
            )
        ) {
            $hasList1ShortClassName = in_array(
                'list1',
                $CSSClassNames
            );

            if (
                $hasList1ShortClassName
            ) {
                $CSSClassNames[] = 'CBMenuView_list1';
            }

            $hasSubmenu1ShortClassName = in_array(
                'submenu1',
                $CSSClassNames
            );

            if (
                $hasSubmenu1ShortClassName
            ) {
                $CSSClassNames[] = 'CBMenuView_submenu1';
            }
        } else {
            /**
             * If none of the built in class names have been used, add the
             * default style.
             */
            $CSSClassNames[] = 'CBMenuView_default';
        }

        $menuItems = CBMenu::getMenuItems(
            $menu
        );

        if (
            count($menuItems) < 5
        ) {
            $CSSClassNames[] = 'few';
        }

        $CSSClassNames = implode(
            ' ',
            $CSSClassNames
        );

        $titleAsHTML = cbhtml(
            CBModel::valueToString(
                $menu,
                'title'
            )
        );

        $titleURI = CBModel::valueToString(
            $menu,
            'titleURI'
        );

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
                <div class="left">
                    <a class="title" <?= $titleHREFAttribute ?>>
                        <?= $titleAsHTML ?>
                    </a>
                </div>
                <div class="center">
                    <a class="title" <?= $titleHREFAttribute ?>>
                        <?= $titleAsHTML ?>
                    </a>
                </div>
                <div class="right"></div>
            </div>
            <div class="CBMenuView_items menu_items">
                <div class="CBMenuView_container container">
                    <?php

                    if (!empty($menu->title)) {
                        ?>

                        <div class="CBMenuView_containerTitle title">
                            <a <?= $titleHREFAttribute ?>>
                                <?= $titleAsHTML ?>
                            </a>
                        </div>

                        <?php
                    }

                    ?>

                    <ul>
                        <li class="CBMenuView_listItemTitle title">
                            <a <?= $titleHREFAttribute ?>>
                                <span><?= $titleAsHTML ?></span>
                            </a>
                        </li>

                        <?php


                        array_walk(
                            $menuItems,
                            function (
                                $menuItemModel
                            ) use (
                                $selectedItemName
                            ) {
                                CBMenuItem::render(
                                    $menuItemModel,
                                    $selectedItemName
                                );
                            }
                        );

                        ?>
                    </ul>
                </div>
            </div>
        </nav>

        <?php
    }
    /* CBView_render() */



    /* -- accessors -- */



    static function
    setCSSClassNames(
        stdClass $menuViewModel,
        string $newCSSClassNames
    ): void {
        $menuViewModel->CSSClassNames = $newCSSClassNames;
    }



    /**
     * @param object $menuViewModel
     *
     * @return CBID|null
     */
    static function
    getMenuModelCBID(
        stdClass $menuViewModel
    ): ?string {
        return CBModel::valueAsCBID(
            $menuViewModel,
            'menuID'
        );
    }
    /* setMenuModelCBID() */



    /**
     * @param object $menuViewModel
     * @param string|null $menuModelCBID
     *
     * @return void
     */
    static function
    setMenuModelCBID(
        stdClass $menuViewModel,
        ?string $newMenuModelCBID
    ): void {
        $menuViewModel->menuID = $newMenuModelCBID;
    }
    /* setMenuModelCBID() */

}
