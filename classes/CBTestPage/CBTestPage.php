<?php

/**
 * This is an example and test of a page class. Use it as a template for
 * building other page classes.
 */
final class CBTestPage {

    /**
     * @param [{stdClass}] $tuples
     *
     * @return null
     */
    public static function modelsWillSave(array $tuples) {
        $models = array_map(function($tuple) { return $tuple->model; }, $tuples);
        CBPages::save($models);
    }

    /**
     * @param [{hex160}] $IDs
     */
    public static function modelsWillDelete(array $IDs) {
        CBPages::deletePagesByID($IDs);
    }

    /**
     * @return {string}
     */
    public static function modelToSearchText(stdClass $model) {
        return "{$model->title} {$model->description}";
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForResponsivePages';
        CBHTMLOutput::begin();
        CBHTMLOutput::setTitleHTML('Test');

        echo "<h1 style=\"padding: 100px; text-align: center;\">{$model->titleAsHTML}</h1>";

        CBHTMLOutput::render();
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBPages::specToModel($spec);

        return $model;
    }
}
