<?php

/**
 * @deprecated 2019_07_07 use CBMenuView
 */
final class CBThemedMenuView {

    /**
     * @return void
     */
    static function fetchMenuItemOptionsForAjax(): void {
        $response = new CBAjaxResponse();
        $menuID = $_POST['menuID'];

        if (empty($menuID)) {
            $menu = false;
        } else {
            $menu = CBModels::fetchModelByID($menuID);
        }

        if ($menu === false) {
            $options = [];
        } else {
            $options = array_map(
                function ($menuItem) {
                    return (object)[
                        'value' => $menuItem->name,
                        'title' => $menuItem->text
                    ];
                },
                $menu->items
            );
        }

        $response->menuItemOptions = $options;
        $response->wasSuccessful = true;
        $response->send();
    }
    /* fetchMenuItemOptionsForAjax() */


    /**
     * @return object
     */
    static function fetchMenuItemOptionsForAjaxPermissions(): stdClass {
        return (object)[
            'group' => 'Administrators'
        ];
    }
    /* fetchMenuItemOptionsForAjaxPermissions() */


    /**
     * @return void
     */
    static function fetchMenusForAjax(): void {
        $response   = new CBAjaxResponse();
        $SQL        = <<<EOT

            SELECT      LOWER(HEX(`ID`)) AS `value`, `title`
            FROM        `CBModels`
            WHERE       `className` = 'CBMenu'
            ORDER BY    `title`

EOT;

        $menus = CBDB::SQLToObjects($SQL);
        $response->menus = ($menus !== false) ? $menus : [];
        $response->wasSuccessful = true;
        $response->send();
    }
    /* fetchMenusForAjax() */


    /**
     * @return object
     */
    static function fetchMenusForAjaxPermissions(): stdClass {
        return (object)[
            'group' => 'Administrators'
        ];
    }
    /* fetchMenusForAjaxPermissions() */


    /**
     * @param object $model
     * @param ?object $args
     *
     * @return void
     */
    static function renderMenuAsHTML(
        stdClass $model,
        stdClass $args = null
    ): void {
        if (empty($model->items)) {
            return;
        }

        $themeID = CBModel::value($args, 'themeID');

        $class = implode(
            ' ',
            CBTheme::IDToCSSClasses($themeID)
        );

        $class = "CBThemedMenuView {$class}";

        CBTheme::useThemeWithID($themeID);

        ?> <div class="<?= $class ?>"><ul> <?php

            foreach($model->items as $item) {
                $selected = (
                    !empty($args->selectedItemName) &&
                    $args->selectedItemName === $item->name
                );

                CBThemedMenuView::renderMenuItem(
                    $item,
                    [
                        'selected' => $selected
                    ]
                );
            }

        ?> </ul></div> <?php
    }
    /* renderMenuAsHTML() */


    /**
     * @param object $menuItem
     * @param bool? $args['selected']
     *
     * @return void
     */
    static function renderMenuItem(stdClass $menuItem, array $args = []): void {
        $selected = false;
        extract($args, EXTR_IF_EXISTS);

        $classes = [];

        if (!empty($menuItem->name)) {
            $classes[] = $menuItem->name;
        }

        if ($selected) {
            $classes[] = "selected";
        }

        $classes = implode(' ', $classes);

        ?>

        <li class="<?= $classes ?>">
            <a href="<?= $menuItem->URLAsHTML ?>">
                <span><?= $menuItem->textAsHTML ?></span>
            </a>
        </li>

        <?php
    }
    /* renderMenuItem() */


    /**
     * @param object $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        if (empty($model->menuID)) {
            return;
        }

        $menu = CBModelCache::fetchModelByID($model->menuID);

        CBThemedMenuView::renderMenuAsHTML($menu, $model);
    }
    /* CBView_render() */


    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object) [
            'menuID' => CBModel::valueAsID($spec, 'menuID'),
            'selectedItemName' => trim(
                CBModel::valueToString($spec, 'selectedItemName')
            ),
            'themeID' => CBModel::valueAsID($spec, 'themeID'),
        ];
    }
    /* CBModel_build() */
}
