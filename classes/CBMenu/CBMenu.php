<?php

final class CBMenu {

    /**
     * @return stdClass
     */
    static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Menus',
            'singularTitle' => 'Menu',
            'userGroup' => 'Administrators',
        ]);
    }

    /**
     * @param string? $spec->title
     * @param array? $spec->items
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $model          = CBModels::modelWithClassName(__CLASS__);
        $model->title   = isset($spec->title) ? (string)$spec->title : '';
        $model->items   = isset($spec->items) ? array_map('CBMenuItem::specToModel', $spec->items) : [];

        return $model;
    }
}
