<?php

final class CBMenu {

    /**
     * @return object
     */
    static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Menus',
            'singularTitle' => 'Menu',
            'userGroup' => 'Administrators',
        ]);
    }

    /**
     * @param array? $spec->items
     * @param string? $spec->title
     * @param string? $spec->titleURI
     *
     * @return object
     */
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'title' => CBModel::value($spec, 'title', '', 'trim'),
            'titleURI' => CBModel::value($spec, 'titleURI', '', 'trim'),
        ];

        $items = CBModel::valueAsArray($spec, 'items');
        $model->items = array_map('CBMenuItem::specToModel', $items);

        return $model;
    }
}
