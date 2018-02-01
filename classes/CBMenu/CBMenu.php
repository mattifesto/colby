<?php

final class CBMenu {

    /**
     * @param array? $spec->items
     * @param string? $spec->title
     * @param string? $spec->titleURI
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'items' => CBModel::valueToModels($spec, 'items'),
            'title' => CBModel::value($spec, 'title', '', 'trim'),
            'titleURI' => CBModel::value($spec, 'titleURI', '', 'trim'),
        ];

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
