<?php

/**
 * This template is very important. It represents the base model for editable
 * pages.
 *
 * It will eventually move to a page base class such as CBPage or a class
 * extending CBPage. But until then this is the place. Any changes or notes
 * on the model should be documented in this file.
 *
 * 2014.09.25 Version 2
 *
 *  Added the `created` and `updated` properties. When pages were first
 *  developed this information was provided by the `ColbyArchive` class. But
 *  `ColbyArchive` has been deprecated and for pages that don't use it, this
 *  information is not available.
 *
 *  The design of these properties requires that if the properties do not exist
 *  it should not cause an error. If they do not exist, it is okay for a process
 *  to set them to reasonable values if it needs them. It is okay to set the
 *  `created` property to the same value as the `updated` property if the
 *  `created` property is not yet set. There's no need to try to guess when the
 *  page was actually created if that information is not readily available.
 *
 * 2014.09.26 Version 3
 *
 *  Added the `listClassNames` propery which holds an array of list class
 *  names representing the lists which include this page.
 */
class CBPageTemplate {

    /**
     * @return stdClass
     */
    public static function model() {

        $model                          = new stdClass();
        $model->created                 = null;
        $model->dataStoreID             = null;
        $model->description             = '';
        $model->descriptionHTML         = '';
        $model->groupID                 = null;
        $model->isPublished             = false;
        $model->listClassNames          = array();
        $model->publicationTimeStamp    = null;
        $model->publishedBy             = null;
        $model->rowID                   = null;
        $model->schema                  = 'CBPage';
        $model->schemaVersion           = 3;
        $model->sections                = array();
        $model->thumbnailURL            = null;
        $model->title                   = '';
        $model->titleHTML               = '';
        $model->updated                 = null;
        $model->URI                     = null;
        $model->URIIsStatic             = false;

        return $model;
    }

    /**
     * @return string
     */
    public static function title() {

        return 'Blank Page';
    }
}
