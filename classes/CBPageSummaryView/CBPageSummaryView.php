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
 *      created: int (timestamp)
 *      description: string
 *      image: CBImage model
 *      isPublished: bool
 *      publicationTimeStamp: int (timestamp)
 *      title: string
 *      updated: int (timestamp)
 *      URI: string
 *
 * 2015.10.29 TODO
 * This class is a work in progress. Many, if not most, of the properties are
 * mis-named. However, this is easily fixable over time. To make changes to the
 * property names of this class you just have to make the change and then walk
 * through the rows of the `ColbyPages` table and regenerate the `keyValueData`.
 * Because this class is already in heavy use as of this writing the properties
 * are staying the same for now.
 *
 * 2016.10.27 TODO
 * The schema of this model will be updated by adding tasks which will verify
 * that all page models are in compliance change or notify of those that aren't.
 *
 * 2018.02.06 TODO
 * A near future update to this class should remove the thumbnailURL property.
 */
final class CBPageSummaryView {

    /**
     * @param object $pageModel
     *
     * @return object
     */
    static function viewPageModelToModel(stdClass $pageModel) {
        return (object)[
            'className' => __CLASS__,
            'ID' => CBModel::value($pageModel, 'ID'),
            'created' => CBModel::value($pageModel, 'created', 0, 'intval'),
            'dataStoreID' => CBModel::value($pageModel, 'ID'),
            'description' => CBModel::value($pageModel, 'description', ''),
            'image' => CBModel::value($pageModel, 'image'),
            'isPublished' => CBModel::value($pageModel, 'isPublished'),
            'publicationTimeStamp' => CBModel::value($pageModel, 'publicationTimeStamp'),
            'publishedBy' => CBModel::value($pageModel, 'publishedBy'),
            'thumbnailURL' => CBModel::value($pageModel, 'thumbnailURL'),
            'title' => CBModel::value($pageModel, 'title'),
            'updated' => CBModel::value($pageModel, 'modified'),
            'URI' => CBModel::value($pageModel, 'URI'),
        ];
    }
}
