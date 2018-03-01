<?php

final class CBViewPage {

    /**
     * @param model $spec
     *
     *      {
     *          image: model?
     *          thumbnailURL: string?
     *
     *              See the documentation for image and thumbnailURL on the
     *              CBPage::toSummary() function.
     *      }
     *
     * @return model
     */
    static function CBModel_build($spec) {
        $ID = CBModel::value($spec, 'ID', '');
        $time = time();
        $model = (object)[
            'classNameForKind' => CBModel::valueToString($spec, 'classNameForKind'),
            'classNameForSettings' => CBModel::valueToString($spec, 'classNameForSettings'),
            'description' => trim(CBModel::valueToString($spec, 'description')),
            'isPublished' => (bool)CBModel::value($spec, 'isPublished'),
            'iteration' => 0, /* deprecated */
            'publishedBy' => CBModel::valueAsInt($spec, 'publishedBy'),
            'selectedMainMenuItemName' => CBModel::valueToString($spec, 'selectedMainMenuItemName'),
            'title' => trim(CBModel::valueToString($spec, 'title')),
        ];

        // URI

        $model->URI = CBConvert::stringToURI(CBModel::valueToString($spec, 'URI'));

        if ($model->URI === '') {
            $model->URI = $ID;
        }

        // publicationTimeStamp

        $model->publicationTimeStamp = CBModel::valueAsInt($spec, 'publicationTimeStamp');

        if ($model->publicationTimeStamp === null && $model->isPublished) {
            $model->publicationTimeStamp = $time;
        }

        // image

        $imageSpec = CBModel::valueAsModel($spec, 'image', ['CBImage']);

        if ($imageSpec) {
            $model->image = CBModel::build($imageSpec);
        }

        if (empty($model->image)) {
            $model->thumbnailURL = CBModel::valueToString($spec, 'thumbnailURL');
        } else {
            // The preference is not to set null properties but we set this one
            // for backward compatability.
            $model->thumbnailURL = null;
        }

        $model->layout = CBModel::build(CBModel::valueAsModel($spec, 'layout'));

        $model->sections = [];
        $sectionSpecs = CBModel::valueToArray($spec, 'sections');
        foreach ($sectionSpecs as $sectionSpec) {
            if ($sectionModel = CBModel::build($sectionSpec)) {
                $model->sections[] = $sectionModel;
            }
        }

        /**
         * Computed values
         */

        $model->thumbnailURLAsHTML = cbhtml($model->thumbnailURL);
        $model->URIAsHTML = cbhtml($model->URI);

        return $model;
    }

    /**
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        $title = CBModel::valueToString($model, 'title');
        $description = CBModel::valueToString($model, 'description');

        $strings = [
            $title,
            $description,
            CBModel::toSearchText(CBModel::value($model, 'layout')),
        ];

        $publicationTimeStamp = CBModel::valueAsInt($model, 'publicationTimeStamp');

        $info = CBHTMLOutput::pageInformation();
        $info->description = $description;
        $info->ID = CBModel::valueAsID($model, 'ID');
        $info->publishedTimestamp = empty($model->isPublished) ? null : $publicationTimeStamp;
        $info->title = $title;

        return implode(
            ' ',
            array_values(array_filter(array_merge(
                $strings,
                array_map(
                    'CBModel::toSearchText',
                    CBModel::valueToArray($model, 'sections')
                )
            )))
        );
    }

    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        if ($image = CBModel::valueAsObject($spec, 'image')) {
            $spec->image = CBImage::fixAndUpgrade($image);
        }

        if ($layout = CBModel::valueAsModel($spec, 'layout')) {
            $spec->layout = CBModel::upgrade($layout);
        } else {
            unset($spec->layout);
        }

        $spec->sections = array_values(array_filter(array_map(
            'CBModel::upgrade',
            CBModel::valueToArray($spec, 'sections')
        )));

        return $spec;
    }

    /**
     * @param model $model
     *
     * @return object
     */
    static function CBPage_toSummary(stdClass $model): stdClass {
        return (object)[
            'description' => CBModel::valueToString($model, 'description'),
            'URI' => CBModel::valueToString($model, 'URI'),

            'created' => CBModel::valueAsInt($model, 'created'),
            'updated' => CBModel::valueAsInt($model, 'modified'),

            'isPublished' => (bool)CBModel::value($model, 'isPublished'),
            'publicationTimeStamp' => CBModel::valueAsInt($model, 'publicationTimeStamp'),

            'image' => CBModel::valueAsModel($model, 'image', ['CBImage']),
            'thumbnailURL' => CBModel::valueToString($model, 'thumbnailURL'),

            /* deprecated? is an int, should be a hex160 */
            'publishedBy' => CBModel::valueAsInt($model, 'publishedBy'),

            /* deprecated */
            'dataStoreID' => CBModel::valueAsID($model, 'ID'),
        ];
    }

    /**
     * @param model $model
     *
     * @return [object]
     */
    static function CBView_toSubviews(stdClass $model): array {
        return CBModel::valueToArray($model, 'sections');
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
     * @param model $model
     *
     *      {
     *          sections: [model]?
     *      }
     *
     * @return void
     */
    static function CBPage_render($model): void {
        $model = CBViewPage::upgradeRenderModel($model);

        // The `upgradeRenderModel` function will return `false` when the query
        // string has values that are unrecognized and indicate that this page
        // does not exist.
        if ($model === false) {
            include Colby::findHandler('handle-default.php');
            return;
        }

        $publicationTimeStamp = CBModel::value($model, 'publicationTimeStamp');
        $title = CBConvert::valueToString(CBModel::value($model, 'title'));
        $description = CBConvert::valueToString(CBModel::value($model, 'description'));

        $info = CBHTMLOutput::pageInformation();
        $info->description = $description;
        $info->ID = CBModel::value($model, 'ID', '');
        $info->image = CBModel::valueAsModel($model, 'image', ['CBImage']);
        $info->imageURL = CBModel::valueToString($model, 'thumbnailURL');
        $info->publishedTimestamp = empty($model->isPublished) ? null : $publicationTimeStamp;
        $info->selectedMainMenuItemName = CBModel::value($model, 'selectedMainMenuItemName');
        $info->title = $title;

        CBHTMLOutput::begin();

        if ($model->classNameForSettings) {
            CBHTMLOutput::$classNameForSettings = $model->classNameForSettings;
        }

        $renderContentCallback = function () use ($model) {
            $sections = CBModel::valueToArray($model, 'sections');
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
