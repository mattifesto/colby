<?php

/**
 * The data stored in the `keyValueData` column of the `ColbyPages` table is
 * a JSON encoded model usually created by this class.
 *
 * Here is a list of the recommended properties for `keyValueData` models. Like
 * many modern models an unset value is also valid, especially due to the
 * historically changing nature of this schema.
 *
 *      ID: hex160
 *      title: string
 *      titleHTML: string
 *      description: string
 *      descriptionHTML: string
 *      URI: string
 *      thumbnailURL: string
 *      isPublished: bool
 *      publicationTimeStamp: int (timestamp)
 *      created: int (timestamp)
 *      updated: int (timestamp)
 *
 * 2016.10.27 TODO
 * The schema of this model will be updated by adding tasks which will verify
 * that all page models are in compliance change or notify of those that aren't.
 *
 * 2015.10.29 TODO
 * This class is a work in progress. Many, if not most, of the properties are
 * mis-named. However, this is easily fixable over time. To make changes to the
 * property names of this class you just have to make the change and then walk
 * through the rows of the `ColbyPages` table and regenerate the `keyValueData`.
 * Because this class is already in heavy use as of this writing the properties
 * are staying the same for now.
 */
final class CBPageSummaryView {

    /**
     * To avoid duplicating property validation this function assumes the model
     * parameter has been generated with the `CBPages::specToModel` function or
     * another function that properly validates and sets the reserved page model
     * properties. It is a the responsibility of the caller to make sure this is
     * true.
     *
     * For instance, the $pageModel->titleAsHTML value is assumed to have
     * already been escaped for use in HTML.
     *
     * NOTE: This model has a thumbnailURL propery which holds the page model's
     * encodedURLForThumbnail value which is not escaped for HTML. Future
     * versions of this structure should have both.
     *
     * @param stdClass $pageModel
     *
     * @return stdClass
     */
    public static function pageModelToModel(stdClass $pageModel) {
        $model = (object)[
            'ID' => $pageModel->ID,
            'className' => __CLASS__,
        ];
        $model->created = $pageModel->created;
        $model->dataStoreID = $pageModel->ID;
        $model->description = $pageModel->description;
        $model->descriptionHTML = $pageModel->descriptionAsHTML;
        $model->isPublished = isset($pageModel->published);
        $model->publicationTimeStamp = $pageModel->published;
        $model->publishedBy = null;
        $model->thumbnailURL = $pageModel->encodedURLForThumbnail;
        $model->title = $pageModel->title;
        $model->titleHTML = $pageModel->titleAsHTML;
        $model->updated = $pageModel->modified;
        $model->URI = $pageModel->dencodedURIPath;

        return $model;
    }

    /**
     * To avoid duplicating property validation this function assumes the model
     * parameter has been generated with the `CBViewPage::specToModel` function or
     * another function that properly validates and sets the reserved page model
     * properties. It is a the responsibility of the caller to make sure this is
     * true.
     *
     * For instance, the $pageModel->titleHTML value is assumed to have already
     * been escaped for use in HTML.
     *
     * @param stdClass $pageModel
     *
     * @return stdClass
     */
    public static function viewPageModelToModel(stdClass $pageModel) {
        $model = (object)[
            'ID' => $pageModel->ID,
            'className' => __CLASS__,
        ];
        $model->created = $pageModel->created;
        $model->dataStoreID = $pageModel->ID;
        $model->description = $pageModel->description;
        $model->descriptionHTML = $pageModel->descriptionHTML;
        $model->isPublished = $pageModel->isPublished;
        $model->publicationTimeStamp = $pageModel->publicationTimeStamp;
        $model->publishedBy = $pageModel->publishedBy;
        $model->thumbnailURL = $pageModel->thumbnailURL;
        $model->title = $pageModel->title;
        $model->titleHTML = $pageModel->titleHTML;
        $model->updated = $pageModel->modified;
        $model->URI = $pageModel->URI;

        return $model;
    }
}
