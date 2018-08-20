<?php

final class CBMenu {

    /**
     * If an item with the same name as the provided item already exists in the
     * menu, the provided item will replace that item. If no item with the same
     * name exists in the menu, the provided item will be appended to the menu.
     *
     * @param model $menu
     * @param model $item
     *
     * @return void
     */
    static function addOrReplaceItem(stdClass $menu, stdClass $item): void {
        $items = CBModel::valueToArray($menu, 'items');
        $name = CBModel::valueToString($item, 'name');
        $index = CBModel::indexOf($items, 'name', $name);

        if ($index === null) {
            array_push($items, $item);
        } else {
            $items[$index] = $item;
        }

        $menu->items = $items;
    }

    /**
     * @param model $spec
     *
     *      {
     *          items: ?[model]
     *          title: ?string
     *          titleURI: ?string
     *      }
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'title' => trim(CBModel::valueToString($spec, 'title')),
            'titleURI' => trim(CBModel::valueToString($spec, 'titleURI')),
        ];

        /* items */

        $model->items = [];
        $itemSpecs = CBModel::valueToArray($spec, 'items');

        foreach ($itemSpecs as $itemSpec) {
            if ($itemModel = CBModel::build($itemSpec)) {
                $model->items[] = $itemModel;
            }
        }

        return $model;
    }

    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        $spec->items = array_values(array_filter(array_map(
            'CBModel::upgrade',
            CBModel::valueToArray($spec, 'items')
        )));

        return $spec;
    }

    /**
     * If an item with the provided name exists in the menu it will be removed.
     *
     * @param model $menu
     * @param string $name
     *
     * @return void
     */
    static function removeItemByName(stdClass $menu, string $name): void {
        $items = CBModel::valueToArray($menu, 'items');
        $index = CBModel::indexOf($items, 'name', $name);

        if ($index !== null) {
            unset($items[$index]);
            $menu->items = array_values($items);
        }
    }

    /**
     * @param mixed $model
     * @param string $selectedMenuItemName
     *
     * @return object|null
     */
    static function selectedMenuItem($model, $selectedMenuItemName): ?stdClass {
        $items = CBConvert::valueToArray(CBModel::value($model, 'items'));

        foreach ($items as $item) {
            $name = CBConvert::valueToString(CBModel::value($item, 'name'));

            if ($name === $selectedMenuItemName) {
                return $item;
            }
        }

        return null;
    }
}
