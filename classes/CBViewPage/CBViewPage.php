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

        $spec = CBModels::fetchSpecByID($ID);

        if ($spec === false) {
            $IDToCopy = CBModel::value($args, 'IDToCopy');

            if (CBHex160::is($IDToCopy)) {
                $spec = CBModels::fetchSpecByID($IDToCopy);

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

        if (empty($model->layout->className)) {
            $renderContent = function () use ($model) {
                echo '<main>';
                $sections = CBModel::valueToArray($model, 'sections');
                array_walk($sections, 'CBView::render');
                echo '</main>';
            };

            $pageFrameClassName = CBPageFrame::defaultClassName();

            CBPageFrame::render($pageFrameClassName, $renderContent);
        } else {
            $renderContentCallback = function () use ($model) {
                $sections = CBModel::valueToArray($model, 'sections');
                array_walk($sections, 'CBView::render');
            };

            CBHTMLOutput::requireClassName($model->layout->className);

            if (is_callable($renderLayout = "{$model->layout->className}::render")) {
                call_user_func($renderLayout, $model->layout, $renderContentCallback);
            }
        }

        CBHTMLOutput::render();
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
