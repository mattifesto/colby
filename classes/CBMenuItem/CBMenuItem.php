<?php

final class CBMenuItem {

    /**
     * @param object $menuItemModel
     *
     * @return string
     */
    static function CBMenuItem_name(stdClass $menuItemModel): string {
        return CBModel::valueToString($menuItemModel, 'name');
    }

    /**
     * @param object $menuItemModel
     *
     * @return void
     */
    static function CBMenuItem_render(stdClass $menuItemModel): void {
        $textAsHTML = cbhtml(CBModel::valueToString($menuItemModel, 'text'));
        $URLAsHTML = cbhtml(CBModel::valueToString($menuItemModel, 'URL'));

        ?>

        <a class="CBMenuItem" href="<?= $URLAsHTML ?>"><span><?= $textAsHTML ?></span></a>

        <?php
    }

    /**
     * @param model $spec
     *
     *      {
     *          name: string?
     *          submenuID: ID?
     *          text: string?
     *          URL: string?
     *      }
     *
     * @return model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'name' => CBModel::valueToString($spec, 'name'),
            'submenuID' => CBModel::valueAsID($spec, 'submenuID'),
            'text' => CBModel::valueToString($spec, 'text'),
            'URL' => CBModel::valueToString($spec, 'URL'),
        ];

        /**
         * These properties are deprecated. When they are confirmed to be
         * unused remove them.
         */
        $model->textAsHTML = cbhtml($model->text);
        $model->URLAsHTML = cbhtml($model->URL);

        return $model;
    }

    /**
     * If the menu item is not hidden, this menu item will render a list item
     * element.
     *
     * @param model $menuItemModel
     * @param string $selectedMenuItemName
     *
     * @return void
     */
    static function render(
        stdClass $menuItemModel,
        string $selectedMenuItemName = ''
    ): void {
        $className = CBModel::valueToString($menuItemModel, 'className');

        if (empty($className)) {
            $className = "CBMenuItem";
        }

        CBHTMLOutput::requireClassName($className);

        if (is_callable($function = "{$className}::CBMenuItem_isHidden")) {
            $isHidden = call_user_func($function, $menuItemModel);

            if ($isHidden) {
                return;
            }
        }

        $name = '';

        if (is_callable($function = "{$className}::CBMenuItem_name")) {
            $name = call_user_func($function, $menuItemModel);
        }

        $classes = 'CBMenuView_menuItem';

        if ($name !== '' && $name === $selectedMenuItemName) {
            $classes .= ' selected';
        }

        echo "<li class=\"{$classes}\">";

        if (is_callable($function = "{$className}::CBMenuItem_render")) {
            call_user_func($function, $menuItemModel);
        }

        echo '</li>';
    }
}
