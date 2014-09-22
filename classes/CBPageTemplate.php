<?php

class CBPageTemplate {

    /**
     * @return stdClass
     */
    public static function model() {

        $model                          = new stdClass();
        $model->schema                  = 'CBPage';
        $model->schemaVersion           = 1;
        $model->rowID                   = null;
        $model->dataStoreID             = null;
        $model->groupID                 = null;
        $model->title                   = '';
        $model->titleHTML               = '';
        $model->description             = '';
        $model->descriptionHTML         = '';
        $model->URI                     = null;
        $model->URIIsStatic             = false;
        $model->publicationTimeStamp    = null;
        $model->isPublished             = false;
        $model->publishedBy             = null;
        $model->thumbnailURL            = null;
        $model->sections                = array();

        return $model;
    }

    /**
     * @return string
     */
    public static function title() {

        return 'Blank Page';
    }
}
