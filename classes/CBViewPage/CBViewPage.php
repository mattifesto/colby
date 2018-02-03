<?php

final class CBViewPage {

    /**
     * @param object $model
     *
     * @return [object]
     */
    static function CBView_toSubviews(stdClass $model) {
        return CBModel::valueAsArray($model, 'sections');
    }

    /**
     * This function loads the spec for a page regardless of whether the latest
     * spec is saved in the CBModels table or in an older spec file.
     *
     * @param hex160 $ID
     * @param bool $create
     *      If a spec doesn't already exist for the ID, an empty one will be
     *      created if this parameter is true. There are enough processes for
     *      pages that this option has measurable benefits.
     *
     * @return stdClass|false
     */
    static function fetchSpecByID($ID, $create = false) {
        $spec = CBModels::fetchSpecByID($ID);

        if (empty($spec)) {
            $iteration = CBViewPage::iterationForID(['ID' => $ID]);

            if ($iteration !== false) {
                $spec = CBViewPage::specWithID($ID, $iteration);
            }

            if (empty($spec) && $create) {
                $spec = (object)[
                    'className' => __CLASS__,
                    'ID' => $ID,
                ];
            }
        } else if ($spec->className !== 'CBViewPage') {
            throw new RuntimeException("The spec with the following ID is not a CBViewPage: {$ID}");
        }


        return $spec;
    }

    /**
     * @param object $args
     *
     *      {
     *          ID: hex160
     *          IDToCopy: hex160?
     *      }
     *
     * @return object|false
     */
    static function CBAjax_fetchSpec($args) {
        $ID = CBModel::value($args, 'ID');

        if (!CBHex160::is($ID)) {
            throw new InvalidArgumentException("The value \"{$ID}\" provided as the ID argument is not a valid 160-bit hexadecimal value.");
        }

        $spec = CBViewPage::fetchSpecByID($ID);

        if ($spec === false) {
            $IDToCopy = CBModel::value($args, 'IDToCopy');

            if (CBHex160::is($IDToCopy)) {
                $spec = CBViewPage::fetchSpecByID($IDToCopy);

                if ($spec === false) {
                    throw new RuntimeException("No spec was found for the page ID: {$IDToCopy}");
                }

                // Perform the copy
                $spec->ID = $ID;
                $spec->title = isset($spec->title) ? "{$spec->title} Copy" : 'Copied Page';
                unset($spec->dataStoreID);
                unset($spec->isPublished);
                unset($spec->iteration);
                unset($spec->publicationTimeStamp);
                unset($spec->publishedBy);
                unset($spec->URI);
                unset($spec->URIIsStatic);
                unset($spec->version);
            }
        }

        return $spec;
    }

    /**
     * @return string
     */
    static function CBAjax_fetchSpec_group() {
        return 'Administrators';
    }

    /**
     * @deprecated This will not be necessary when all pages are saved as models.
     *
     * @return int|false
     */
    static function iterationForID($args) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $IDAsSQL = ColbyConvert::textToSQL($ID);
        $SQL = "SELECT `iteration` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$IDAsSQL}')";

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @param [hex160] $IDs
     *
     * @return null
     */
    static function CBModels_willDelete(array $IDs) {
        CBPages::deletePagesByID($IDs);
        CBPages::deletePagesFromTrashByID($IDs);
    }

    /**
     * @param [object] $models
     *
     * @return null
     */
    static function CBModels_willSave(array $models) {
        CBPages::save($models);
    }

    /**
     * @return string
     *  An empty string will be returned if no image is available.
     */
    private static function modelToImageURL($model) {
        if (!empty($model->image)) {
            return CBDataStore::flexpath($model->image->ID, "rw1280.{$model->image->extension}", CBSitePreferences::siteURL());
        } else if (!empty($model->thumbnailURL)) {
            return $model->thumbnailURL;
        } else {
            return '';
        }
    }

    /**
     * @param stdClass $model
     *
     * @return string
     */
    static function CBModel_toSearchText($model) {
        $strings = [
            CBModel::value($model, 'title'),
            CBModel::value($model, 'description'),
        ];

        if ($layout = CBModel::valueAsObject($model, 'layout')) {
            $strings[] = CBModel::toSearchText($layout);
        }

        $publicationTimeStamp = CBModel::value($model, 'publicationTimeStamp');

        CBPageContext::push([
            'descriptionAsHTML' => $model->descriptionHTML,
            'ID' => $model->ID,
            'publishedTimestamp' => empty($model->isPublished) ? null : $publicationTimeStamp,
            'titleAsHTML' => $model->titleHTML,
        ]);

        $views = CBModel::valueAsObjects($model, 'sections');
        $strings = array_merge($strings, array_map('CBModel::toSearchText', $views));

        CBPageContext::pop();

        $strings = array_filter($strings);
        return implode(' ', $strings);
    }

    /**
     * @param string? $model->titleHTML
     *
     *      This should be safe for HTML, but not contain actual HTML tags.
     *
     * @param [object]? $model->sections
     *
     * @return null
     */
    static function CBPage_render($model) {
        $model = CBViewPage::upgradeRenderModel($model);

        // The `upgradeRenderModel` function will return `false` when the query
        // string has values that are unrecognized and indicate that this page
        // does not exist.
        if ($model === false) {
            include Colby::findHandler('handle-default.php');
            return;
        }

        $publicationTimeStamp = CBModel::value($model, 'publicationTimeStamp');

        CBPageContext::push([
            'descriptionAsHTML' => CBModel::value($model, 'descriptionHTML', ''),
            'ID' => CBModel::value($model, 'ID', ''),
            'imageURL' => CBViewPage::modelToImageURL($model),
            'publishedTimestamp' => empty($model->isPublished) ? null : $publicationTimeStamp,
            'selectedMainMenuItemName' => CBModel::value($model, 'selectedMainMenuItemName'),
            'titleAsHTML' => CBModel::value($model, 'titleHTML', ''),
        ]);

        CBHTMLOutput::begin();

        if ($model->classNameForSettings) {
            CBHTMLOutput::$classNameForSettings = $model->classNameForSettings;
        }

        $renderContentCallback = function () use ($model) {
            $sections = CBModel::valueAsArray($model, 'sections');
            array_walk($sections, 'CBView::render');
        };

        if (!empty($model->layout->className)) {
            CBHTMLOutput::requireClassName($model->layout->className);

            if (is_callable($renderLayout = "{$model->layout->className}::render")) {
                call_user_func($renderLayout, $model->layout, $renderContentCallback);
            }
        } else {
            $renderContentCallback();
        }

        CBHTMLOutput::render();

        CBPageContext::pop();
    }

    /**
     * @param object? $spec->image
     *
     *      An image that represents the page, to be used for thumbnails and
     *      other images to represent the page. Must be a valid CBImage. If this
     *      is specifiect, `thumbnailURL` will be ignored.
     *
     * @param string? $spec->thumbnailURL
     *
     *      This image that represents the page. This property should only be
     *      specified if the image's location is non-standard, such as on
     *      another website. This is inferior to `image` but is not deprecated
     *      because it's the only solution for images in non-standard locations.
     *
     *      While it's not deprecated, its use should be avoided because it
     *      most likely will be deprecated at some point in the future.
     *
     * @return object
     */
    static function CBModel_toModel($spec) {
        $ID = CBModel::value($spec, 'ID', '');
        $time = time();
        $model = (object)[
            'className' => __CLASS__,
            'classNameForKind' => CBModel::value($spec, 'classNameForKind', '', 'trim'),
            'classNameForSettings' => CBModel::value($spec, 'classNameForSettings', '', 'trim'),
            'description' => CBModel::value($spec, 'description', '', 'strval'),
            'isPublished' => CBModel::value($spec, 'isPublished', false, 'boolval'),
            'iteration' => 0, /* deprecated */
            'publishedBy' => CBModel::value($spec, 'publishedBy', null, 'intval'),
            'selectedMainMenuItemName' => CBModel::value($spec, 'selectedMainMenuItemName', '', 'trim'),
            'title' => CBModel::value($spec, 'title', '', 'trim'),
            'URI' => CBModel::value($spec, 'URI', $ID, function ($value) use ($ID) {
                $value = CBConvert::stringToURI($value);

                if ($value === '') {
                    return $ID;
                } else {
                    return $value;
                }
            }),
        ];

        $model->publicationTimeStamp = isset($spec->publicationTimeStamp) ? (int)$spec->publicationTimeStamp : ($model->isPublished ? $time : null);

        /**
         * Page image
         */

        $model->image = CBModel::valueAsSpecToModel($spec, 'image', 'CBImage');

        if (empty($model->image)) {
            $model->thumbnailURL = CBModel::value($spec, 'thumbnailURL');
        } else {
            // The preference is not to set null properties but we set this one
            // for backward compatability.
            $model->thumbnailURL = null;
        }

        $model->layout = CBModel::valueToModel($spec, 'layout');
        $model->sections = CBModel::valueToModels($spec, 'sections');

        /**
         * Computed values
         */

        $model->descriptionHTML = ColbyConvert::textToHTML($model->description);
        $model->thumbnailURLAsHTML = ColbyConvert::textToHTML($model->thumbnailURL);
        $model->titleHTML = ColbyConvert::textToHTML($model->title);
        $model->URIAsHTML = ColbyConvert::textToHTML($model->URI);

        return $model;
    }

    /**
     * This function loads older specs for pages that haven't yet been saved
     * to the CBModels table.
     *
     * @return stdClass|false
     */
    static function specWithID($ID, $iteration) {
        $directory = CBDataStore::directoryForID($ID);

        if (is_file($filepath = "{$directory}/spec-{$iteration}.json")) {

            // Pages edited after installing version 137 of Colby will have a
            // spec file saved for each iteration.

        } else if (is_file($filepath = "{$directory}/spec.json")) {

            // Pages edited for a brief time before version 137 will have a
            // spec file with this name.

        } else if (is_file($filepath = "{$directory}/model.json")) {

            // Pages that were last edited before the spec/model split use the
            // model file as the spec file. TODO: It would be nice to
            // create an update that would go through every view page and
            // canonicalize this filename so that these final two conditions
            // can be removed.

        } else {
            return false;
        }

        $spec = json_decode(file_get_contents($filepath));

        if (empty($spec->ID)) {
            $spec->ID = $spec->dataStoreID;
        }

        if (!isset($spec->iteration)) {
            $spec->iteration = 1;
        }

        return $spec;
    }

    /**
     * This function performs a render time transform on a page model. This may
     * mean upgrading old models, but more likely it means transforming model
     * properties in response to query variables. This is how a single page can
     * become multiple pages using the query string and the page kind.
     *
     * @return stdClass|false
     *  Returns the modified model. A false value is returned when the query
     *  variables lead to a page that does not exist and a 404 page should be
     *  rendered.
     */
    private static function upgradeRenderModel($model) {
        if (!isset($model->updated)) {
            $model->updated = time();
        }

        if (!isset($model->created)) {
            $model->created = $model->updated;
        }

        // 2015.09.19 classNameForSettings
        if (!isset($model->classNameForSettings)) {
            $model->classNameForSettings = '';
        }

        return $model;
    }
}
