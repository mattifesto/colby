<?php

final class CBMenuItem {

    /**
     * @return stdClass
     */
    public static function info() {
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
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model              = CBModels::modelWithClassName(__CLASS__);
        $model->name        = isset($spec->name) ? ColbyConvert::textToStub($spec->name) : '';
        $model->text        = isset($spec->text) ? (string)$spec->text : '';
        $model->textAsHTML  = ColbyConvert::textToHTML($model->text);
        $model->URL         = isset($spec->URL) ? trim($spec->URL) : '';
        $model->URLAsHTML   = ColbyConvert::textToHTML($model->URL);

        return $model;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBMenuItem/{$filename}";
    }
}
