<?php

class CBPageSummaryView extends CBView {

    /**
     * The properties on the model are a subset of the properties for a page
     * model excluding properties that are not useful for displaying a page
     * summary and properties that are deprecated.
     *
     * @return instance type
     */
    public static function init() {

        $view   = parent::init();
        $model  = $view->model;

        $model->created                 = null;
        $model->dataStoreID             = null;
        $model->description             = '';
        $model->descriptionHTML         = '';
        $model->isPublished             = false;
        $model->publicationTimeStamp    = null;
        $model->publishedBy             = null;
        $model->thumbnailURL            = null;
        $model->title                   = '';
        $model->titleHTML               = '';
        $model->updated                 = null;
        $model->URI                     = null;

        return $view;
    }
}