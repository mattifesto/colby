<?php

final class CBMenu {

    /**
     * @param array? $spec->items
     * @param string? $spec->title
     * @param string? $spec->titleURI
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec) {
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
