<?php

final class CBViewPage {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v418.css', cbsysurl()),
        ];
    }

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
        $model = (object)[
            'classNameForKind' => CBModel::valueToString($spec, 'classNameForKind'),
            'classNameForSettings' => CBModel::valueToString($spec, 'classNameForSettings'),
            'description' => trim(CBModel::valueToString($spec, 'description')),
            'frameClassName' => CBModel::valueToString($spec, 'frameClassName'),
            'isPublished' => (bool)CBModel::value($spec, 'isPublished'),
            'iteration' => 0, /* deprecated */
            'publicationTimeStamp' => CBModel::valueAsInt($spec, 'publicationTimeStamp'),
            'publishedBy' => CBModel::valueAsInt($spec, 'publishedBy'),
            'title' => trim(CBModel::valueToString($spec, 'title')),
            'URI' => CBConvert::stringToURI(CBModel::valueToString($spec, 'URI')),
        ];

        /**
         * selectedMenuItemNames
         *
         * The property value on the spec is a string, on the model an array.
         */

        $selectedMenuItemNames = CBModel::valueToNames($spec, 'selectedMenuItemNames');

        if (empty($selectedMenuItemNames)) {
            /* deprecated */
            $selectedMainMenuItemName = CBModel::valueToString($spec, 'selectedMainMenuItemName');

            if (!empty($selectedMainMenuItemName)) {
                $selectedMenuItemNames = [$selectedMainMenuItemName];
            }
        }

        $model->selectedMenuItemNames = $selectedMenuItemNames;

        /* URI */

        $ID = CBModel::valueAsID($spec, 'ID');

        if (empty($model->URI) && !empty($ID)) {
            $model->URI = $ID;
        }

        if ($model->publicationTimeStamp === null && $model->isPublished) {
            $model->publicationTimeStamp = time();
        }

        /* image */

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

        $layoutSpec = CBModel::valueAsModel($spec, 'layout');

        if ($layoutSpec) {
            $model->layout = CBModel::build($layoutSpec);
        }

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
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_prepareCopy(stdClass $spec): stdClass {
        unset($spec->isPublished);
        unset($spec->publicationTimeStamp);
        unset($spec->publishedBy);
        unset($spec->URI);
        unset($spec->URIIsStatic);

        return $spec;
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

        CBViewPage::initializePageInformation($model);

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

        /**
         * @NOTE 2018.04.13
         *
         *      This upgrade exists to help sites update all of the page specs
         *      because soon the classNameForSettings property will be required.
         */
        if (empty($spec->classNameForSettings)) {
            $title = CBMessageMarkup::stringToMarkup(
                CBModel::valueToString($spec, 'title')
            );

            $defaultPageSettingsClassName = CBPageSettings::defaultClassName();

            if (empty($defaultPageSettingsClassName)) {
                $severity = 4;
                $message = <<<EOT

                    The (classNameForSettings (code)) property is not set on the
                    spec for the page with the title "{$title}".

                    (CBPageSettings::defaultClassName\(\) (code)) returns
                    (null (code)) so the spec can't be upgraded.

EOT;
            } else {
                $severity = 6;
                $spec->classNameForSettings = $defaultPageSettingsClassName;
                $message = <<<EOT

                    The (classNameForSettings (code)) property has been set to
                    "{$defaultPageSettingsClassName}" because is was not set on
                    the spec for the page with the title "{$title}".

EOT;
            }

            CBLog::log((object)[
                'className' => __CLASS__,
                'severity' => $severity,
                'message' => $message,
            ]);
        }

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
     * @param model $model
     * @param [model] $subviews
     *
     * @return void
     */
    static function CBView_setSubviews(stdClass $model, array $subviews): void {
        $model->sections = $subviews;
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
        $publicationTimeStamp = CBModel::value($model, 'publicationTimeStamp');
        $title = CBConvert::valueToString(CBModel::value($model, 'title'));
        $description = CBConvert::valueToString(CBModel::value($model, 'description'));

        CBViewPage::initializePageInformation($model);
        CBHTMLOutput::begin();

        try {
            if (empty($model->layout->className)) {

                /**
                 * @TODO 2018_04_07
                 *
                 *      The main element is the container of the CBViewPage class.
                 *      The CBViewPage class should allow you to add classes and
                 *      styles to this element. It does not currently allow that, so
                 *      for now the CBViewPage_default class name is added which
                 *      eventually can be removed by specifying the "custom" class
                 *      name manually.
                 */

                $renderContent = function () use ($model) {
                    echo '<main class="CBViewPage CBViewPage_default">';
                    $sections = CBModel::valueToArray($model, 'sections');
                    array_walk($sections, 'CBView::render');
                    echo '</main>';
                };

                $frameClassName = CBModel::valueToString($model, 'frameClassName');

                CBPageFrame::render($frameClassName, $renderContent);
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
        } catch (Throwable $throwable) {
            CBHTMLOutput::reset();
            CBErrorHandler::report($throwable);
            CBErrorHandler::renderErrorReportPage($throwable);
        }
    }
    /* CBPage_render() */


    /**
     * This function copies the appropriate model information into the
     * CBHTMLOutput page information object.
     *
     * @param model $model
     *
     * @return void
     */
    private static function initializePageInformation(stdClass $model): void {
        $pageInformation = CBHTMLOutput::pageInformation();

        if (empty($model->isPublished)) {
            $publishedTimestamp = null;
        } else {
            $publishedTimestamp = CBModel::valueAsInt($model, 'publicationTimeStamp');
        }

        CBModel::merge($pageInformation, (object)[
            'classNameForPageSettings' => CBModel::valueToString($model, 'classNameForSettings'),
            'description' => CBModel::valueToString($model, 'description'),
            'ID' => CBModel::valueAsID($model, 'ID'),
            'image' => CBModel::valueAsModel($model, 'image', ['CBImage']),
            'imageURL' => CBModel::valueToString($model, 'thumbnailURL'),
            'publishedTimestamp' => $publishedTimestamp,
            'selectedMenuItemNames' => CBViewPage::selectedMenuItemNames($model),
            'title' => CBModel::valueToString($model, 'title'),
        ]);
    }

    /**
     * Use this function to get the array of selected menu item names. The first
     * name is for the selected main menu item, the second name is for the
     * selected secondary menu item, etc.
     *
     * This function handles the transition between the deprecated
     * 'selectedMainMenuItemName' property and its replacement, the
     * 'selectedMenuItemNames' property.
     *
     * @param object $model
     *
     * @return [string]
     */
    static function selectedMenuItemNames(stdClass $model): array {
        $selectedMenuItemNames = CBModel::valueToArray($model, 'selectedMenuItemNames');

        if (empty($selectedMenuItemNames)) {
            $selectedMainMenuItemName = trim(CBModel::valueToString($model, 'selectedMainMenuItemName'));

            if (empty($selectedMainMenuItemName)) {
                return [];
            } else {
                return [$selectedMainMenuItemName];
            }
        }

        return $selectedMenuItemNames;
    }

    /**
     * @param string $moniker
     *
     *      This function will not trim or make any other modifications to the
     *      moniker.
     *
     * @return ID
     */
    static function monikerToID(string $moniker): string {
        return sha1("0e64b8a8110db365de4e49d6d890a7d9a2dd60fa {$moniker}");
    }
}
