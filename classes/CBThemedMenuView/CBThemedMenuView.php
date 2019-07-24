<?php

/**
 * @deprecated 2019_07_07 use CBMenuView
 */
final class CBThemedMenuView {

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
