<?php

final class CBViewPage {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v418.css', cbsysurl()),
        ];
    }



    /* -- CBInstall interfaces -- */



    /**
     * Developers installing a CBViewPage model using the CBInstall_install()
     * interface may not know which classes that they require. Implementing this
     * interface on this class allows them to just require the one class they
     * know about.
     *
     * @return void
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
            'CBPages',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     *      {
     *          image: model?
     *          thumbnailURL: string?
     *
     *              See the documentation for image and thumbnailURL on the
     *              CBPage::toSummary() function.
     *      }
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $spec
    ) {
        $classNameForSettings = CBModel::valueAsName(
            $spec,
            'classNameForSettings'
        );

        if ($classNameForSettings === null) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This CBViewPage spec does not have a valid
                    "classNameForSettings" property value. A CBViewPage spec
                    must have a valid "classNameForSettings" property value to
                    be built into a model.

                EOT),
                $spec,
                'c0a955387d1cf7c82bd0662c06916102646aaf54'
            );
        }

        $model = (object)[
            'classNameForKind' => CBModel::valueToString(
                $spec,
                'classNameForKind'
            ),

            'classNameForSettings' => $classNameForSettings,

            'description' => trim(
                CBModel::valueToString(
                    $spec,
                    'description'
                )
            ),

            'frameClassName' => CBModel::valueToString(
                $spec,
                'frameClassName'
            ),

            'isPublished' => CBModel::valueToBool(
                $spec,
                'isPublished'
            ),

            'iteration' => 0, /* deprecated */

            'publicationTimeStamp' => CBModel::valueAsInt(
                $spec,
                'publicationTimeStamp'
            ),

            'publishedByUserCBID' => CBModel::valueAsCBID(
                $spec,
                'publishedByUserCBID'
            ),

            'title' => trim(
                CBModel::valueToString(
                    $spec,
                    'title'
                )
            ),

            'URI' => CBConvert::stringToURI(
                CBModel::valueToString(
                    $spec,
                    'URI'
                )
            ),
        ];

        /**
         * selectedMenuItemNames
         *
         * The property value on the spec is a string, on the model an array.
         */

        $selectedMenuItemNames = CBModel::valueToNames(
            $spec,
            'selectedMenuItemNames'
        );

        if (empty($selectedMenuItemNames)) {
            /* deprecated */
            $selectedMainMenuItemName = CBModel::valueToString(
                $spec,
                'selectedMainMenuItemName'
            );

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
            $model->thumbnailURL = CBModel::valueToString(
                $spec,
                'thumbnailURL'
            );
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
    /* CBModel_build() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_prepareCopy(stdClass $spec): stdClass {
        unset($spec->isPublished);
        unset($spec->publicationTimeStamp);
        unset($spec->publishedByUserCBID);
        unset($spec->URI);
        unset($spec->URIIsStatic);

        return $spec;
    }



    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        $title = CBModel::valueToString(
            $model,
            'title'
        );

        $description = CBModel::valueToString(
            $model,
            'description'
        );

        $strings = [
            $title,
            $description,
        ];

        $frameClassName = CBModel::valueToString(
            $model,
            'frameClassName'
        );

        if ($frameClassName !== '') {
            array_push(
                $strings,
                $frameClassName
            );

            $function = "{$frameClassName}::CBViewPage_getFrameSearchText";

            if (is_callable($function)) {
                $searchText = call_user_func(
                    $function
                );

                array_push(
                    $strings,
                    $searchText
                );
            }
        }


        /**
         * @deprecated 2020_11_09
         *
         *      ViewPage layouts have been deprecated. When they are completely
         *      removed the else block below can also be removed.
         */
        else {
            $viewPageLayouModel = CBModel::valueAsModel(
                $model,
                'layout'
            );

            if ($viewPageLayouModel !== null) {
                $searchText = CBModel::toSearchText(
                    $viewPageLayouModel
                );

                array_push(
                    $strings,
                    $searchText
                );
            }
        }


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
    /* CBModel_toSearchText() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_upgrade(
        stdClass $spec
    ): stdClass {

        /**
         * @NOTE 2020_11_11
         *
         *      The way search text is generated for CBViewPage models was
         *      altered so all existing pages should be rebuilt.
         */
        $spec->CBViewPage_versionDate = '2020_11_11';


        if ($image = CBModel::valueAsObject($spec, 'image')) {
            $spec->image = CBImage::fixAndUpgrade($image);
        }


        if ($layout = CBModel::valueAsModel($spec, 'layout')) {
            $spec->layout = CBModel::upgrade($layout);
        } else {
            unset($spec->layout);
        }


        $spec->sections = array_values(
            array_filter(
                array_map(
                    'CBModel::upgrade',
                    CBModel::valueToArray($spec, 'sections')
                )
            )
        );


        /* done */

        return $spec;
    }
    /* CBModel_upgrade() */



    /* -- CBModels interfaces -- -- -- -- */



    /**
     * @param [CBID] $IDs
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



    /* -- CBPage interfaces -- -- -- -- -- */



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

        /**
         * @NOTE 2019_12_12
         *
         *      This exception handler should probably be moved into
         *      CBPage::render() to handle all page rendering errors.
         */

        set_exception_handler('CBViewPage::CBPage_render_handleError');

        try {
            $publicationTimeStamp = CBModel::value(
                $model,
                'publicationTimeStamp'
            );

            $title = CBModel::valueToString(
                $model,
                'title'
            );

            $description = CBModel::valueToString(
                $model,
                'description'
            );

            CBViewPage::initializePageInformation($model);
            CBHTMLOutput::begin();

            $frameClassName = CBModel::valueAsName(
                $model,
                'frameClassName'
            );

            if ($frameClassName !== null) {

                /**
                 * @TODO 2018_04_07
                 *
                 *      The main element is the container of the CBViewPage
                 *      class. The CBViewPage class should allow you to add
                 *      classes and styles to this element. It does not
                 *      currently allow that, so for now the CBViewPage_default
                 *      class name is added which eventually can be removed by
                 *      specifying the "custom" class name manually.
                 */

                $renderContent = function () use ($model) {
                    echo '<main class="CBViewPage CBViewPage_default">';

                    $sections = CBModel::valueToArray($model, 'sections');

                    array_walk(
                        $sections,
                        function ($viewModel) {
                            CBView::render($viewModel);
                        }
                    );

                    echo '</main>';
                };

                CBPageFrame::render($frameClassName, $renderContent);
            } else {
                $renderContentCallback = function () use ($model) {
                    $viewModels = CBModel::valueToArray(
                        $model,
                        'sections'
                    );

                    array_walk(
                        $viewModels,
                        function ($viewModel) {
                            CBView::render($viewModel);
                        }
                    );
                };

                $layoutClassName = CBModel::valueAsName(
                    $model,
                    'layout.className'
                );

                CBHTMLOutput::requireClassName($layoutClassName);

                $renderLayoutFunctionName = "{$layoutClassName}::render";

                if (is_callable($renderLayoutFunctionName)) {
                    call_user_func(
                        $renderLayoutFunctionName,
                        $model->layout,
                        $renderContentCallback
                    );
                } else {
                    call_user_func(
                        $renderContentCallback
                    );
                }
            }

            CBHTMLOutput::render();
        } catch (Throwable $renderError) {
            CBHTMLOutput::reset();

            $pageError = new CBExceptionWithValue(
                (
                    'This page model generate an error ' .
                    'while it was rendering.'
                ),
                $model,
                '1c9e0e1c0e89467c9e1c061a3e6e3f735e0cc92e',
                0,
                $renderError
            );

            throw $pageError;
        }

        restore_exception_handler();
    }
    /* CBPage_render() */



    /**
     * This function is set as the exception handler by
     * CBViewPage::CBPage_render() for the duration of
     * CBViewPage::CBPage_render().
     *
     * @param Throwable $error
     *
     * @return void
     */
    static function CBPage_render_handleError(
        Throwable $error
    ): void {
        CBErrorHandler::report($error);
        CBErrorHandler::renderErrorReportPage($error);
    }
    /* CBPage_render_handleError() */



    /**
     * @param object $model
     *
     * @return object
     */
    static function CBPage_toSummary(stdClass $model): stdClass {
        return (object)[
            'description' => CBModel::valueToString(
                $model,
                'description'
            ),

            'URI' => CBModel::valueToString(
                $model,
                'URI'
            ),

            'created' => CBModel::valueAsInt(
                $model,
                'created'
            ),

            'updated' => CBModel::valueAsInt(
                $model,
                'modified'
            ),

            'isPublished' => CBModel::valueToBool(
                $model,
                'isPublished'
            ),

            'publicationTimeStamp' => CBModel::valueAsInt(
                $model,
                'publicationTimeStamp'
            ),

            'image' => CBModel::valueAsModel(
                $model,
                'image',
                [
                    'CBImage',
                ]
            ),

            'thumbnailURL' => CBModel::valueToString(
                $model,
                'thumbnailURL'
            ),

            'publishedByUserCBID' => CBModel::valueAsInt(
                $model,
                'publishedByUserCBID'
            ),

            /* deprecated */
            'dataStoreID' => CBModel::valueAsID(
                $model,
                'ID'
            ),
        ];
    }
    /* CBPage_toSummary() */



    /* -- CBView interfaces -- -- -- -- -- */



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
    static function CBView_setSubviews(
        stdClass $model,
        array $subviews
    ): void {
        $model->sections = $subviews;
    }



    /* -- functions -- -- -- -- -- */



    /**
     * This function copies the appropriate model information into the
     * CBHTMLOutput page information object.
     *
     * @param model $model
     *
     * @return void
     */
    private static function
    initializePageInformation(
        stdClass $model
    ): void {
        $pageInformation = CBHTMLOutput::pageInformation();

        if (empty($model->isPublished)) {
            $publishedTimestamp = null;
        } else {
            $publishedTimestamp = CBModel::valueAsInt(
                $model,
                'publicationTimeStamp'
            );
        }

        CBModel::merge(
            $pageInformation,
            (object)[
                'classNameForPageSettings' => CBModel::valueToString(
                    $model,
                    'classNameForSettings'
                ),

                'description' => CBModel::valueToString(
                    $model,
                    'description'
                ),

                'ID' => CBModel::valueAsID(
                    $model,
                    'ID'
                ),

                'image' => CBModel::valueAsModel(
                    $model,
                    'image',
                    [
                        'CBImage',
                    ]
                ),

                'imageURL' => CBModel::valueToString(
                    $model,
                    'thumbnailURL'
                ),

                'publishedTimestamp' => $publishedTimestamp,

                'selectedMenuItemNames' => CBViewPage::selectedMenuItemNames(
                    $model
                ),

                'title' => CBModel::valueToString(
                    $model,
                    'title'
                ),
            ]
        );
    }
    /* initializePageInformation() */



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
        $selectedMenuItemNames = CBModel::valueToArray(
            $model,
            'selectedMenuItemNames'
        );

        if (empty($selectedMenuItemNames)) {
            $selectedMainMenuItemName = trim(
                CBModel::valueToString(
                    $model,
                    'selectedMainMenuItemName'
                )
            );

            if (empty($selectedMainMenuItemName)) {
                return [];
            } else {
                return [$selectedMainMenuItemName];
            }
        }

        return $selectedMenuItemNames;
    }
    /* selectedMenuItemNames() */

}
