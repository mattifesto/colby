<?php

/**
 * @deprecated 2019_07_07 use CBMenuView
 */
final class CBThemedMenuView {

    /**
     * @return null
     */
    static function fetchMenuItemOptionsForAjax() {
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
            $options = array_map(function ($menuItem) {
                return (object)['value' => $menuItem->name, 'title' => $menuItem->text];
            }, $menu->items);
        }

        $response->menuItemOptions = $options;
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function fetchMenuItemOptionsForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function fetchMenusForAjax() {
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

    /**
     * @return stdClass
     */
    static function fetchMenusForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param CBMenu $model
     * @param hex160? $args->themeID
     * @param string? $args->selectedItemName
     *
     * @return null
     */
    static function renderMenuAsHTML(stdClass $model, stdClass $args = null) {
        if (empty($model->items)) {
            return;
        }

        $themeID = CBModel::value($args, 'themeID');
        $class = implode(' ', CBTheme::IDToCSSClasses($themeID));
        $class = "CBThemedMenuView {$class}";

        CBTheme::useThemeWithID($themeID);

        ?> <div class="<?= $class ?>"><ul> <?php

            foreach($model->items as $item) {
                $selected = (!empty($args->selectedItemName) && $args->selectedItemName === $item->name);
                CBThemedMenuView::renderMenuItem($item, ['selected' => $selected]);
            }

        ?> </ul></div> <?php
    }

    /**
     * @param stdClass $menuItem
     * @param bool? $args['selected']
     *
     * @return null
     */
    static function renderMenuItem(stdClass $menuItem, array $args = []) {
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
            <a href="<?= $menuItem->URLAsHTML ?>"><span><?= $menuItem->textAsHTML ?></span></a>
        </li>

        <?php
    }

    /**
     * @param hex160? $model->menuID
     * @param hex160? $model->themeID
     * @param string? $model->selectedItemName
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        if (empty($model->menuID)) {
            return;
        }

        $menu = CBModelCache::fetchModelByID($model->menuID);

        CBThemedMenuView::renderMenuAsHTML($menu, $model);
    }

    /**
     * @param hex160? $spec->menuID
     * @param string? $spec->selectedItemName
     * @param hex160? $spec->themeID
     *
     * @return stdClass
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $model->menuID = isset($spec->menuID) ? trim($spec->menuID) : '';
        $model->selectedItemName = isset($spec->selectedItemName) ? trim($spec->selectedItemName) : '';
        $model->themeID = isset($spec->themeID) ? trim($spec->themeID) : '';

        return $model;
    }
}
