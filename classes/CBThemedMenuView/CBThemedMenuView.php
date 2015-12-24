<?php

final class CBThemedMenuView {

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBResponsiveEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBThemedMenuView::URL('CBThemedMenuViewEditorFactory.js')
        ];
    }

    /**
     * @return {stdClass}
     */
    public static function fetchMenuItemOptionsForAjax() {
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
                return (object)['value' => $menuItem->name, 'textContent' => $menuItem->text];
            }, $menu->items);
        }

        $response->menuItemOptions = $options;
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchMenuItemOptionsForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return {stdClass}
     */
    public static function fetchMenusForAjax() {
        $response   = new CBAjaxResponse();
        $SQL        = <<<EOT

            SELECT      LOWER(HEX(`ID`)) AS `value`, `title` AS `textContent`
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
     * @return {stdClass}
     */
    public static function fetchMenusForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return {stdClass}
     */
    public static function fetchThemesForAjax() {
        $response   = new CBAjaxResponse();
        $SQL        = <<<EOT

            SELECT      LOWER(HEX(`ID`)) AS `value`, `title` AS `textContent`
            FROM        `CBModels`
            WHERE       `className` = 'CBThemedMenuViewTheme'
            ORDER BY    `title`

EOT;

        $themes = CBDB::SQLToObjects($SQL);
        $response->themes = ($themes !== false) ? $themes : [];
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchThemesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param {stdClass} $menuItem
     *
     * @return null
     */
    public static function renderMenuItem(stdClass $menuItem, array $args = []) {
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
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if (empty($model->menuID)) {
            return;
        }

        $menu = CBModelCache::fetchModelByID($model->menuID);

        if ($menu === false || empty($menu->items)) {
            return;
        }

        if (empty($model->themeID)) {
            $class = "CBThemedMenuView NoTheme";
        } else {
            $class = "CBThemedMenuView T{$model->themeID}";

            CBHTMLOutput::addCSSURL(CBDataStore::toURL([
                'ID' => $model->themeID,
                'filename' => 'CBThemedMenuViewTheme.css'
            ]));
        }

        ?>

        <div class="<?= $class ?>"><ul> <?php

            foreach($menu->items as $item) {
                $selected = (!empty($model->selectedItemName) && $model->selectedItemName === $item->name);
                CBThemedMenuView::renderMenuItem($item, ['selected' => $selected]);
            }

        ?> </ul></div>

        <?php
    }

    /**
     * @param {stdClass} $spec
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $model->menuID = isset($spec->menuID) ? trim($spec->menuID) : '';
        $model->selectedItemName = isset($spec->selectedItemName) ? trim($spec->selectedItemName) : '';
        $model->themeID = isset($spec->themeID) ? trim($spec->themeID) : '';

        return $model;
    }

    /**
     * @param {string} $filename
     *
     * @return {string}
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
