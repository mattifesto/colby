<?php

final class
CBMenuItem
{
    /**
     * @param object $menuItemModel
     *
     * @return string
     */
    static function CBMenuItem_name(stdClass $menuItemModel): string {
        return CBModel::valueToString($menuItemModel, 'name');
    }
    // CBMenuItem_name()



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
    // CBMenuItem_render()



    /**
     * @param object $menuItemSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $menuItemSpec
    ): ?stdClass
    {
        $menuItemModel =
        (object)
        [
            'name' =>
            CBModel::valueToString(
                $menuItemSpec,
                'name'
            ),

            'submenuID' =>
            CBModel::valueAsID(
                $menuItemSpec,
                'submenuID'
            ),

            'text' =>
            CBModel::valueToString(
                $menuItemSpec,
                'text'
            ),

            'URL' =>
            CBModel::valueToString(
                $menuItemSpec,
                'URL'
            ),
        ];



        /**
         * These properties are deprecated. When they are confirmed to be
         * unused remove them.
         */

        $menuItemModel->textAsHTML =
        cbhtml(
            $menuItemModel->text
        );

        $menuItemModel->URLAsHTML =
        cbhtml(
            $menuItemModel->URL
        );

        return
        $menuItemModel;
    }
    // CBModel_build()



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
    // render()
}
