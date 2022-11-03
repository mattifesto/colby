<?php

final class
CBViewPage {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v418.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



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
    ): stdClass
    {
        $model = (object)[
            'classNameForKind' => CBModel::valueToString(
                $spec,
                'classNameForKind'
            ),

            'description' => trim(
                CBModel::valueToString(
                    $spec,
                    'description'
                )
            ),

            'iteration' => 0, /* deprecated */

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
        ];

        CBViewPage::setAdministrativeTitle(
            $model,
            CBViewPage::getAdministrativeTitle(
                $spec
            )
        );

        CBViewPage::setFrameClassName(
            $model,
            CBViewPage::getFrameClassName(
                $spec
            )
        );

        CBViewPage::setIsPublished(
            $model,
            CBViewPage::getIsPublished(
                $spec
            )
        );


        CBViewPage::setPageSettingsClassName(
            $model,
            CBViewPage::getPageSettingsClassName(
                $spec
            )
        );

        CBViewPage::setPublicationTimestamp(
            $model,
            CBViewPage::getPublicationTimestamp(
                $spec
            )
        );

        /**
         * URI
         *
         * @NOTE 2021_08_01
         *
         *      If the spec has a URI property, then the URI must be a valid URI
         *      or we will throw an exception. An empty URI is a valid value but
         *      won't result in a usable URL. The characters allowed in a URI
         *      are fairly restricted right now, but more will be allowed over
         *      time.
         *
         *      If we generated a URL, for instance by using the title, then
         *      allowing more characters in URLs might make the URLs of existing
         *      pages change, so if a page has a URL it must be specified
         *      exactly in the spec so that changes to processes or allowed
         *      characters won't cause changes to the URLs for pages.
         *
         * @TODO 2021_08_01
         *
         *      The code below reports but does not throw an exception if the
         *      page spec doesn't have a valid URI. In the future, this
         *      exception will be thrown and the code to alter or generate a
         *      valid URI will be removed.
         */

        $specURI = CBViewPage::getURI(
            $spec
        );

        $modelURI = CBConvert::stringToURI(
            $specURI
        );

        if (
            $modelURI !== $specURI
        ) {
            $throwable = new CBExceptionWithValue(
                'This CBViewPage spec does not have a valid URI value.',
                $spec,
                '1c1609f6087a9caaad3ac902689aa076eae1ed4a'
            );

            CBErrorHandler::report(
                $throwable
            );
        }

        CBViewPage::setURI(
            $model,
            $modelURI
        );


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


        if ($model->publicationTimeStamp === null && $model->isPublished) {
            $model->publicationTimeStamp = time();
        }

        /* image */

        $imageSpec =
        CBViewPage::getPrimaryImageModel(
            $spec
        );

        if (
            $imageSpec !==
            null
        ) {
            $model->image =
            CBModel::build(
                $imageSpec
            );
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

        $viewModels = [];

        $viewSpecs = CBViewPage::getViews(
            $spec
        );

        foreach ($viewSpecs as $viewSpec) {
            $viewModel = CBModel::build(
                $viewSpec
            );

            if (
                $viewModel !== null
            ) {
                array_push(
                    $viewModels,
                    $viewModel
                );
            }
        }

        CBViewPage::setViews(
            $model,
            $viewModels
        );

        /**
         * Computed values
         */

        $model->thumbnailURLAsHTML = cbhtml($model->thumbnailURL);
        $model->URIAsHTML = cbhtml($model->URI);

        return $model;
    }
    /* CBModel_build() */



    /**
     * @param object $viewPageModel
     *
     * @return string
     */
    static function
    CBModel_getAdministrativeTitle(
        stdClass $viewPageModel
    ): string
    {
        return
        CBViewPage::getAdministrativeTitle(
            $viewPageModel
        );
    }
    // CBModel_getAdministrativeTitle()



    /**
     * @param <CBViewPage model> $viewPageModelArgument
     *
     * @return <CBImage model>|null
     */
    static function
    CBModel_getPrimaryImageModel(
        stdClass $viewPageModelArgument
    ): ?stdClass
    {
        $primaryImageModel =
        CBViewPage::getPrimaryImageModel(
            $viewPageModelArgument
        );

        return $primaryImageModel;
    }
    // CBModel_getPrimaryImageModel()



    /**
     * @param object $viewPageModel
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $viewPageModel
    ): string
    {
        return
        CBViewPage::getTitle(
            $viewPageModel
        );
    }
    // CBModel_getTitle()



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
    static function
    CBModel_toSearchText(
        stdClass $model
    ): string
    {
        $administrativeTitle =
        CBViewPage::getAdministrativeTitle(
            $model
        );

        $title = CBModel::valueToString(
            $model,
            'title'
        );

        $description = CBModel::valueToString(
            $model,
            'description'
        );

        $strings =
        [
            $administrativeTitle,
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


        CBViewPage::initializePageInformation(
            $model
        );

        $views = CBViewPage::getViews(
            $model
        );

        return implode(
            ' ',
            array_values(
                array_filter(
                    array_merge(
                        $strings,
                        array_map(
                            'CBModel::toSearchText',
                            $views
                        )
                    )
                )
            )
        );
    }
    /* CBModel_toSearchText() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $spec
    ): stdClass
    {
        /**
         * @NOTE 2022_03_05
         *
         *      After working through a number of task failures on a client
         *      website I've come to the understanding that it is a basic
         *      requirement that upgrade be able to always repair invalid
         *      values.
         *
         *      Code that generates invalid URIs or allows invalid URIs to pass
         *      through should be fixed, but upgrade will always create a valid
         *      URI from an invalid URI.
         */

        $originalURI = CBViewPage::getURI(
            $spec
        );

        $updatedURI = substr(
            $originalURI,
            0,
            100
        );

        $updatedURI = CBConvert::stringToURI(
            $updatedURI
        );

        if (
            $updatedURI !== $originalURI
        ) {
            CBErrorHandler::report(
                new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The URI property of a CBViewPage spec was invalid, so it
                        was changed from "${originalURI}" to "${updatedURI}"
                        when the spec was upgraded. Developers should discover
                        the original source of the invalid URI and fix it.

                    EOT),
                    $spec,
                    '34be5ffa0c84d1efec14bc368c398d3ee1b66da9'
                )
            );
        }
        // if

        CBViewPage::setURI(
            $spec,
            $updatedURI
        );



        /**
         * @NOTE 2020_12_22 version 675
         *
         *      The selectedMainMenuItemName propery has been partially
         *      deprecated for a while now but the implementation of the
         *      deprecation was wonky. Now things are cleaned up and we can
         *      implement full removal of the property.
         */

        $selectedMainMenuItemName = trim(
            CBModel::valueToString(
                $spec,
                'selectedMainMenuItemName'
            )
        );

        if ($selectedMainMenuItemName !== '') {
            $selectedMenuItemNames = trim(
                CBModel::valueToString(
                    $spec,
                    'selectedMenuItemNames'
                )
            );

            if ($selectedMenuItemNames === '') {
                $spec->selectedMenuItemNames = $spec->selectedMainMenuItemName;
            }
        }

        unset(
            $spec->selectedMainMenuItemName
        );


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


        $originalViews = CBViewPage::getViews(
            $spec
        );

        $upgradedViews = array_values(
            array_filter(
                array_map(
                    'CBModel::upgrade',
                    $originalViews
                )
            )
        );

        CBViewPage::setViews(
            $spec,
            $upgradedViews
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
    static function
    CBPage_render(
        $model
    ): void {

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

                    $views = CBViewPage::getViews(
                        $model
                    );

                    array_walk(
                        $views,
                        function (
                            $viewModel
                        ) {
                            CBView::render($viewModel);
                        }
                    );

                    echo '</main>';
                };

                CBPageFrame::render($frameClassName, $renderContent);
            } else {
                $renderContentCallback = function () use ($model) {
                    $viewModels = CBViewPage::getViews(
                        $model
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
                CBConvert::stringToCleanLine(<<<EOT

                    This CBViewPage model generated an error while it was
                    rendering.

                EOT),
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

            'URI' => CBViewPage::getURI(
                $model,
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
    static function
    CBView_toSubviews(
        stdClass $model
    ): array {
        return CBViewPage::getViews(
            $model
        );
    }
    /* CBView_toSubviews() */



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
        CBViewPage::setViews(
            $model,
            $subviews
        );
    }



    /* -- accessors -- */



    /**
     * @param object $viewPageModel
     *
     * @return string
     */
    static function
    getAdministrativeTitle(
        stdClass $viewPageModel
    ): string
    {
        return
        CBModel::valueToString(
            $viewPageModel,
            'CBViewPage_administrativeTitle_property'
        );
    }
    /* getAdministrativeTitle() */



    /**
     * @param object $viewPageSpec
     * @param string $newAdministrativeTitle
     *
     * @return void
     */
    static function
    setAdministrativeTitle(
        stdClass $viewPageSpec,
        string $newAdministrativeTitle
    ): void
    {
        $viewPageSpec->CBViewPage_administrativeTitle_property =
        $newAdministrativeTitle;
    }
    /* setAdministrativeTitle() */



    /**
     * @param object $viewPageModel
     *
     * @return string|null
     */
    static function
    getFrameClassName(
        stdClass $viewPageModel
    ): ?string {
        return CBModel::valueAsName(
            $viewPageModel,
            'frameClassName'
        );
    }
    // getFrameClassName()



    /**
     * @param object $viewPageSpec
     * @param string|null $newFrameClassName
     *
     * @return void
     */
    static function
    setFrameClassName(
        stdClass $viewPageSpec,
        ?string $newFrameClassName
    ): void {
        $newFrameClassNameAsName = CBConvert::valueAsName(
            $newFrameClassName
        );

        $viewPageSpec->frameClassName = $newFrameClassNameAsName;
    }
    // setFrameClassName()



    /**
     * @param <CBViewPage model> $viewPageModelArgument
     *
     * @return <CBImage model>|null
     */
    static function
    getPrimaryImageModel(
        stdClass $viewPageModelArgument
    ): ?stdClass
    {
        $primaryImageModel =
        CBModel::valueAsModel(
            $viewPageModelArgument,
            'image',
            'CBImage'
        );

        return $primaryImageModel;
    }
    // getPrimaryImageModel()



    /**
     * @param object %viewPageModel
     *
     * @return bool
     */
    static function
    getIsPublished(
        stdClass $viewPageModel
    ): bool {
        return CBModel::valueToBool(
            $viewPageModel,
            'isPublished'
        );
    }
    /* getIsPublished() */



    /**
     * @param object $viewPageModel
     * @param bool $newIsPublished
     *
     * @return void
     */
    static function
    setIsPublished(
        stdClass $viewPageModel,
        bool $newIsPublished
    ): void {
        $viewPageModel->isPublished = $newIsPublished;

        if (
            $newIsPublished
        ) {
            $publicationTimestamp = CBViewPage::getPublicationTimestamp(
                $viewPageModel
            );

            if (
                $publicationTimestamp === null
            ) {
                CBViewPage::setPublicationTimestamp(
                    $viewPageModel,
                    time()
                );
            }
        }
    }
    /* setIsPublished() */



    /**
     * @param object $viewPageModel
     *
     * @return string|null
     */
    static function
    getPageSettingsClassName(
        stdClass $viewPageModel
    ): ?string {
        return CBModel::valueAsName(
            $viewPageModel,
            'classNameForSettings'
        );
    }
    /* getPageSettingsClassName() */



    /**
     * @param object $viewPageSpec
     * @param string $pageSettingsClassName
     *
     * @return void
     */
    static function
    setPageSettingsClassName(
        stdClass $viewPageSpec,
        string $newPageSettingsClassName
    ): void {
        $newPageSettingsClassNameAsName = CBConvert::valueAsName(
            $newPageSettingsClassName
        );

        if ($newPageSettingsClassNameAsName === null) {
            throw new CBExceptionWithValue(
                'The class name must be a valid Colby name value.',
                $newPageSettingsClassName,
                '7f5a08b9d4d548d3e6972a2e683929f3e4877f73'
            );
        }

        $viewPageSpec->classNameForSettings = $newPageSettingsClassNameAsName;
    }
    /* setPageSettingsClassName() */



    /**
     * @param object $viewPageModel
     *
     * @return int|null
     */
    static function
    getPublicationTimestamp(
        stdClass $viewPageModel
    ): ?int {
        return CBModel::valueAsInt(
            $viewPageModel,
            'publicationTimeStamp'
        );
    }
    /* getPublicationTimestamp() */



    /**
     * @param object $viewPageModel
     * @param int|null $newPublicationTimestamp
     *
     * @return void
     */
    static function
    setPublicationTimestamp(
        stdClass $viewPageModel,
        ?int $newPublicationTimestamp
    ): void {
        $viewPageModel->publicationTimeStamp = $newPublicationTimestamp;
    }
    /* setPublicationTimestamp() */



    /**
     * @param object $viewPageModel
     *
     *      This function works with a spec or model.
     *
     * @return string
     *
     *      Returns the exact string set using
     *      CBViewPage::setSelectedMenuItemNames(). This string is supposed to
     *      contain zero or more valid names, but is not validated.
     *
     * @TODO 2020_12_22
     *
     *      In a future version this model will be altered to use only the
     *      CBViewPage_selectedMenuItemNames property. That property will be
     *      a string on both the spec and the model.
     */
    static function
    getSelectedMenuItemNames(
        stdClass $viewPageModel
    ): string {
        if (
            isset($viewPageModel->selectedMenuItemNames) &&
            is_array($viewPageModel->selectedMenuItemNames)
        ) {
            return implode(
                ' ',
                $viewPageModel->selectedMenuItemNames
            );
        }

        $value = CBModel::valueToString(
            $viewPageModel,
            'selectedMenuItemNames'
        );

        if ($value !== '') {
            return $value;
        }

        $value = CBModel::valueToString(
            $viewPageModel,
            'selectedMainMenuItemName'
        );

        return $value;
    }
    /* getSelectedMenuItemNames() */



    /**
     * @param object $viewPageModel
     *
     *      This function works with a spec or model, although it would rarely
     *      make any sense to use it on a spec.
     *
     * @return [string]
     *
     *      If all of the menu item names are not valid names this function will
     *      return an empty array.
     */
    static function
    getSelectedMenuItemNamesArray(
        stdClass $viewPageModel
    ): array {
        return CBConvert::valueAsNames(
            CBViewPage::getSelectedMenuItemNames(
                $viewPageModel
            )
        ) ?? [];
    }
    /* getSelectedMenuItemNamesArray() */



    /**
     * @param $viewPageSpec
     *
     *      This function is only intended to be used with a spec.
     *
     * @param string $value
     *
     *      This should be a spec or comma separated string of valid names.
     *
     * @return void
     */
    static function
    setSelectedMenuItemNames(
        stdClass $viewPageSpec,
        string $value
    ): void {
        $viewPageSpec->selectedMenuItemNames = $value;
    }
    /* setSelectedMenuItemNames() */



    /**
     * @param object $viewPageSpec
     * @param object $imageSpec
     *
     * @return void
     */
    static function
    setThumbnailImage(
        stdClass $viewPageSpec,
        stdClass $imageSpec
    ): void {
        $imageSpecClassName = CBModel::getClassName(
            $imageSpec
        );

        if ($imageSpecClassName !== 'CBImage') {
            throw new CBExceptionWithValue(
                'The thumbnail image spec must have the class name "CBImage"',
                $imageSpec,
                '6548606092a44ab7dbc7a68ccaba1c12bc8e98f3'
            );
        }

        $viewPageSpec->image = $imageSpec;

        unset($viewPageSpec->thumbnailURL);
    }
    /* setThumbnailImage() */



    /**
     * @param object $viewPageModel
     *
     * @return string
     */
    static function
    getTitle(
        stdClass $viewPageModel
    ): string
    {
        return
        CBModel::valueToString(
            $viewPageModel,
            'title'
        );
    }
    // getTitle()



    /**
     * @param object $viewPageModel
     * @param object $title
     *
     * @return void
     */
    static function
    setTitle(
        stdClass $viewPageModel,
        string $title
    ): void
    {
        $viewPageModel->title =
        $title;
    }
    // setTitle()



    /**
     * @param object $viewPageModel
     *
     * @return [object]
     */
    static function
    getViews(
        stdClass $viewPageModel
    ): array {
        return CBModel::valueToArray(
            $viewPageModel,
            'sections'
        );
    }
    /* getViews() */



    /**
     * @param object $viewPageSpec
     * @param array $viewSpecs
     *
     * @return void
     */
    static function
    setViews(
        stdClass $viewPageSpec,
        array $viewSpecs
    ): void {
        $viewPageSpec->sections = $viewSpecs;
    }
    /* setViews() */



    /**
     * @param object $viewPageSpec
     *
     * @return string
     */
    static function
    getURI(
        stdClass $viewPageSpec
    ): string {
        return CBModel::valueToString(
            $viewPageSpec,
            'URI'
        );
    }
    /* getURI() */



    /**
     * @param object $viewPageSpec
     * @param string $URI
     *
     *      Empty URIs are allowed.
     *
     * @return void
     */
    static function
    setURI(
        stdClass $viewPageSpec,
        string $URI
    ): void {
        $validatedURI = CBConvert::stringToURI(
            $URI
        );

        if (
            $validatedURI !== $URI
        ) {
            throw new CBExceptionWithValue(
                "'${URI}' is not a valid URI value.",
                $viewPageSpec,
                '1bf1d855be02c39c95e325c0547ca54d3119845d'
            );
        }

        $viewPageSpec->URI = $URI;
    }
    /* setURI() */



    /* -- functions -- */



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

                'title' => CBModel::valueToString(
                    $model,
                    'title'
                ),
            ]
        );

        CBHTMLOutput::setSelectedMenuItemNamesArray(
            CBViewPage::getSelectedMenuItemNamesArray(
                $model
            )
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
     * @deprecated 2020_12_22
     *
     *      Use CBViewPage::getSelectedMenuItemNamesArray()
     *
     * @param object $model
     *
     * @return [string]
     */
    static function
    selectedMenuItemNames(
        stdClass $viewPageModel
    ): array {
        return CBViewPage::getSelectedMenuItemNamesArray(
            $viewPageModel
        );
    }
    /* selectedMenuItemNames() */



    /**
     * @NOTE 2021_09_03
     *
     *      This is the first step in creating pages with the new 3 column
     *      standard layout to be used by all pages. I'm not sure if the
     *      eventual form of this will be a CBViewPage but it is for now.
     *
     * @return object
     */
    static function
    standardPageTemplate(
    ): stdClass {
        $standardPageTemplate = CBModel::createSpec(
            'CBViewPage'
        );

        CBViewPage::setViews(
            $standardPageTemplate,
            []
        );

        CBModel::merge(
            $standardPageTemplate,
            (object)[
                'classNameForSettings' => 'CB_StandardPageSettings',
                'frameClassName' => 'CB_StandardPageFrame',
            ]
        );

        return $standardPageTemplate;
    }
    /* standardPageTemplate() */

}
