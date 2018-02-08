<?php

/**
 * A CBPageSummary object is a summary of a model that represents a page. These
 * objects are use to create lists of pages. This object will be stored in the
 * ColbyPages table. The object is not technically a model because:
 *
 *      - its ID is the ID of an already existing model
 *      - its class does not implement any of the CBModel interfaces
 *
 * Here is a list of the recommended properties for CBPageSummary. Like models,
 * an unset value is valid.
 *
 *      ID: hex160
 *
 *      title: string
 *      description: string
 *      URI: string
 *
 *      created: int (timestamp)
 *      updated: int (timestamp)
 *
 *      isPublished: bool
 *      publicationTimeStamp: int (timestamp)
 *
 *      image: model (CBImage)
 *      thumbnailURL: string
 *
 * "image" and "thumbnailURL"
 *
 *      The "image" and "thumbnailURL" properties work together to specify an
 *      image that represents a page. Most of the time, "image" is used. But a
 *      CBImage model can only represent a local Colby image. If a site needs
 *      to represent the page with an image on another site, then "thumbnailURL"
 *      will be set to the URL for that image.
 *
 *      Only one of the two properties should ever be set but if both are set,
 *      "image" takes priority.
 *
 *      The functionality of CBImage is much greater and more flexible that an
 *      image URL. The end goal is to move all images to CBImage. In the future,
 *      CBImage may be able to specify a site where the image resides.
 *      Alternatively, all uses of thumbnailURL may be removed. In either case,
 *      at that point, thumbnailURL should be removed.
 *
 *      This is the official documentation location for the concept of "image"
 *      and "thumbnailURL".
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
 */
final class CBPageSummaryView {

    /**
     * @param model $pageModel
     *
     * @return object
     */
    static function viewPageModelToModel(stdClass $pageModel) {
        return (object)[
            'className' => __CLASS__,
            'ID' => CBModel::value($pageModel, 'ID'),

            'title' => CBModel::value($pageModel, 'title'),
            'description' => CBModel::value($pageModel, 'description', ''),
            'URI' => CBModel::value($pageModel, 'URI'),

            'created' => CBModel::value($pageModel, 'created', 0, 'intval'),
            'updated' => CBModel::value($pageModel, 'modified'),

            'isPublished' => CBModel::value($pageModel, 'isPublished'),
            'publicationTimeStamp' => CBModel::value($pageModel, 'publicationTimeStamp'),

            'image' => CBModel::value($pageModel, 'image'),
            'thumbnailURL' => CBModel::value($pageModel, 'thumbnailURL'),

            /* deprecated? is an int, should be a hex160 */
            'publishedBy' => CBModel::value($pageModel, 'publishedBy'),

            /* deprecated */
            'dataStoreID' => CBModel::value($pageModel, 'ID'),
        ];
    }
}
