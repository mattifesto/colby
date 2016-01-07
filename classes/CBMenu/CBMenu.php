<?php

final class CBMenu {

    /**
     * @return stdClass
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Menus',
            'singularTitle' => 'Menu',
        ]);
    }

    /**
     * @param string? $spec->title
     * @param array? $spec->items
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model          = CBModels::modelWithClassName(__CLASS__);
        $model->title   = isset($spec->title) ? (string)$spec->title : '';
        $model->items   = isset($spec->items) ? array_map('CBMenuItem::specToModel', $spec->items) : [];

        return $model;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBMenu/{$filename}";
    }
}
