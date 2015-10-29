<?php

/**
 * This is an example and test of a page class. Use it as a template for
 * building other page classes.
 */
final class CBTestPage {

    /**
     * @param [{stdclass}] $tuples
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
     * @return null
     */
    public static function renderModelAsHTML($model) {
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
        $model = CBModels::modelWithClassName(__CLASS__, ['ID' => $spec->ID]);
        $model->dencodedURIPath = isset($spec->URIPath) ? CBPages::stringToDencodedURIPath($spec->URIPath) : '';
        $model->dencodedURIPath = ($model->dencodedURIPath === '') ? $spec->ID : $model->dencodedURIPath;
        $model->description = isset($spec->description) ? trim($spec->description) : '';
        $model->descriptionAsHTML = ColbyConvert::textToHTML($model->description);
        $model->encodedURLForThumbnail = isset($spec->encodedURLForThumbnail) ? trim($spec->encodedURLForThumbnail) : '';
        $model->encodedURLForThumbnailAsHTML = ColbyConvert::textToHTML($model->encodedURLForThumbnail);
        $model->published = isset($spec->published) ? (int)$spec->published : null;
        $model->title = CBModels::specToTitle($spec);
        $model->titleAsHTML = ColbyConvert::textToHTML($model->title);

        return $model;
    }
}
