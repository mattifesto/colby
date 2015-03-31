<?php

final class CBPageSummaryView {

    /**
     * The properties on the model are a subset of the properties for a page
     * model excluding properties that are not useful for displaying a page
     * summary and properties that are deprecated.
     *
     * 2015.03.31 TODO:
     *  This class is kind of bizarre. It is used by CBViewPage to create
     *  key-value data, but i'm not sure if or how that data is used. In the
     *  future, usage of the class should be cleaned up.
     *
     * @return instance type
     */
    public static function specToModel(stdClass $spec = null) {
        $model                          = CBView::modelWithClassName(__CLASS__);
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

        return $model;
    }
}
