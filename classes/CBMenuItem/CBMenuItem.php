<?php

final class CBMenuItem {

    /**
     * @return object
     */
    static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Menu Items',
            'singularTitle' => 'Menu Item',
        ]);
    }

    /**
     * @param string? $spec->name
     * @param string? $spec->text
     * @param string? $spec->URL
     *
     * @return object
     */
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'name' => CBModel::value($spec, 'name', '', 'ColbyConvert::textToStub'),
            'text' => CBModel::value($spec, 'text', '', 'strval'),
            'URL' => CBModel::value($spec, 'URL', '', 'trim'),
        ];

        /**
         * These properties are deprecated. When they are confirmed to be
         * unused remove them.
         */
        $model->textAsHTML = cbhtml($model->text);
        $model->URLAsHTML = cbhtml($model->URL);

        return $model;
    }
}
