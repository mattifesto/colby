<?php

final class CBMenu {

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
     * @return void
     */
    static function CBModel_upgrade(stdClass $spec): void {
        $itemSpecs = CBModel::valueToArray($spec, 'items');
        $spec->items = [];

        foreach ($itemSpecs as $itemSpec) {
            if ($itemSpec = CBConvert::valueAsModel($itemSpec)) {
                $spec->items[] = CBModel::upgrade($itemSpec);
            }
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
