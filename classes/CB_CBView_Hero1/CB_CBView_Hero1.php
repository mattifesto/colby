<?php

final class
CB_CBView_Hero1
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.70.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $viewSpec
    ): stdClass
    {
        $viewModel =
        (object)[];

        CB_CBView_Hero1::setAdministrativeTitle(
            $viewModel,
            CB_CBView_Hero1::getAdministrativeTitle(
                $viewSpec
            )
        );

        CB_CBView_Hero1::setAlternativeText(
            $viewModel,
            CB_CBView_Hero1::getAlternativeText(
                $viewSpec
            )
        );

        CB_CBView_Hero1::setImageDestinationURL(
            $viewModel,
            CB_CBView_Hero1::getImageDestinationURL(
                $viewSpec
            )
        );

        CB_CBView_Hero1::setNarrowImage(
            $viewModel,
            CB_CBView_Hero1::getNarrowImage(
                $viewSpec
            )
        );

        $subviewSpecs =
        CB_CBView_Hero1::getSubviews(
            $viewSpec
        );

        $subviewModels =
        array_map(
            function (
                $subviewSpec
            ): stdClass
            {
                return
                CBModel::build(
                    $subviewSpec
                );
            },
            $subviewSpecs
        );

        CB_CBView_Hero1::setSubviews(
            $viewModel,
            $subviewModels
        );

        CB_CBView_Hero1::setWideImage(
            $viewModel,
            CB_CBView_Hero1::getWideImage(
                $viewSpec
            )
        );

        return $viewModel;
    }
    /* CBModel_build() */



    /**
     * @param object $viewModel
     *
     * @return string
     */
    static function
    CBModel_toSearchText(
        stdClass $viewModel
    ): string
    {
        $searchTextSnippets = [
            CB_CBView_Hero1::getAdministrativeTitle(
                $viewModel
            ),
            CB_CBView_Hero1::getAlternativeText(
                $viewModel
            ),
        ];

        $subviewModels =
        CB_CBView_Hero1::getSubviews(
            $viewModel
        );

        foreach (
            $subviewModels as $subviewModel
        ) {
            array_push(
                $searchTextSnippets,
                CBModel::toSearchText(
                    $subviewModel
                )
            );
        }

        return
        implode(
            ' ',
            $searchTextSnippets
        );
    }
    // CBModel_toSearchText()



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $originalViewSpec
    ): stdClass
    {
        $originalSubviewSpecs =
        CB_CBView_Hero1::getSubviews(
            $originalViewSpec
        );

        $upgradedSubviewSpecs =
        array_map(
            function (
                stdClass $originalSubviewSpec
            ): stdClass
            {
                return
                CBModel::upgrade(
                    $originalSubviewSpec
                );
            },
            $originalSubviewSpecs
        );

        CB_CBView_Hero1::setSubviews(
            $originalViewSpec,
            $upgradedSubviewSpecs
        );

        return $originalViewSpec;
    }
    // CBModel_upgrade()



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void
    {
        echo
        CBConvert::stringToCleanLine(<<<EOT

            <div class="CB_CBView_Hero1_root_element">
                <div class="CB_CBView_Hero1_content_element">

        EOT);



        // open wrapper

        $imageDestinationURL =
        CB_CBView_Hero1::getImageDestinationURL(
            $viewModel
        );

        if (
            $imageDestinationURL !== ''
        ) {
            $imageDestinationURLAsHTML =
            cbhtml(
                $imageDestinationURL
            );

            echo
            CBConvert::stringToCleanLine(<<<EOT

                <a
                    href="${imageDestinationURLAsHTML}"
                    class="CB_CBView_Hero1_imageWrapper_element"
                >

            EOT);
        }

        else
        {
            echo
            CBConvert::stringToCleanLine(<<<EOT

                <div
                    class="CB_CBView_Hero1_imageWrapper_element"
                >

            EOT);
        }



        // images

        $wideImageModel =
        CB_CBView_Hero1::getWideImage(
            $viewModel
        );

        $narrowImageModel =
        CB_CBView_Hero1::getNarrowImage(
            $viewModel
        ) ?? $wideImageModel;

        if (
            $wideImageModel === null
        ) {
            $wideImageModel =
            $narrowImageModel;
        }

        $alternativeText =
        CB_CBView_Hero1::getAlternativeText(
            $viewModel
        );

        if (
            $wideImageModel !== null
        ) {
            CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
                $wideImageModel,
                'rw2560',
                10000,
                10000,
                $alternativeText
            );
        }

        if (
            $narrowImageModel !== null
        ) {
            CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
                $narrowImageModel,
                'rw1280',
                1000,
                1000,
                $alternativeText
            );
        }



        // close wrapper

        if (
            $imageDestinationURL !== ''
        ) {
            echo
            '</a>';
        }

        else
        {
            echo
            '</div>';
        }



        // subviews

        $subviewModels =
        CB_CBView_Hero1::getSubviews(
            $viewModel
        );

        array_walk(
            $subviewModels,
            function (
                $subviewModel
            ): void {
                CBView::render(
                    $subviewModel
                );
            }
        );

        echo
        '</div></div>';
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewModel
     *
     * @return string
     */
    static function
    getAdministrativeTitle(
        stdClass $viewModel
    ): string
    {
        return CBModel::valueToString(
            $viewModel,
            'CB_CBView_Hero1_administrativeTitle_property',
        );
    }
    // getAdministrativeTitle()



    /**
     * @param object $viewModel
     * @param string $newAdministrativeTitle
     *
     * @return void
     */
    static function
    setAdministrativeTitle(
        stdClass $viewModel,
        string $newAdministrativeTitle
    ): void
    {
        $viewModel->CB_CBView_Hero1_administrativeTitle_property =
        $newAdministrativeTitle;
    }
    // setAdministrativeTitle()



    /**
     * @param object $viewModel
     *
     * @return string
     */
    static function
    getAlternativeText(
        stdClass $viewModel
    ): string
    {
        return CBModel::valueToString(
            $viewModel,
            'CB_CBView_Hero1_alternativeText_property',
        );
    }
    // getAlternativeText()



    /**
     * @param object $viewModel
     * @param string $newAlternativeText
     *
     * @return void
     */
    static function
    setAlternativeText(
        stdClass $viewModel,
        string $newAlternativeText
    ): void
    {
        $viewModel->CB_CBView_Hero1_alternativeText_property =
        $newAlternativeText;
    }
    // setAlternativeText()



    /**
     * @param objedt $viewModel
     *
     * @return string
     *
     *      Returns '' if there is no image destination URL.
     */
    static function
    getImageDestinationURL(
        stdClass $viewModel
    ): string
    {
        return
        trim(
            CBModel::valueToString(
                $viewModel,
                'CB_CBView_Hero1_imageDestinationURL_property'
            )
        );
    }
    // getImageDestinationURL()



    /**
     * @param $viewModel
     * @param string $newImageDestinationURL
     *
     * @return void
     */
    static function
    setImageDestinationURL(
        stdClass $viewModel,
        string $newImageDestinationURL
    ): void
    {
        $viewModel->CB_CBView_Hero1_imageDestinationURL_property =
        $newImageDestinationURL;
    }
    // setImageDestinationURL()



    /**
     * @param object $viewModel
     *
     * @return object|null
     */
    static function
    getNarrowImage(
        stdClass $viewModel
    ): ?stdClass
    {
        return CBModel::valueAsModel(
            $viewModel,
            'CB_CBView_Hero1_narrowImage_property',
            'CBImage'
        );
    }
    // getNarrowImage()



    /**
     * @param object $viewModel
     * @param object|null $newNarrowImageModel
     *
     * @return void
     */
    static function
    setNarrowImage(
        stdClass $viewModel,
        ?stdClass $newNarrowImageModel
    ): void
    {
        $viewModel->CB_CBView_Hero1_narrowImage_property =
        $newNarrowImageModel;
    }
    // setNarrowImage()



    /**
     * @param object $viewModel
     *
     * @return [object]
     */
    static function
    getSubviews(
        stdClass $viewModel
    ): array
    {
        return CBModel::valueToArray(
            $viewModel,
            'CB_CBView_Hero1_subviews_property'
        );
    }
    // getSubviews()



    /**
     * @param object $viewModel
     * @param [object] $newSubviews
     *
     * @return void
     */
    static function
    setSubviews(
        stdClass $viewModel,
        array $newSubviews
    ): void
    {
        $viewModel->CB_CBView_Hero1_subviews_property =
        array_values(
            $newSubviews
        );
    }
    // setSubviews()



    /**
     * @param object $viewModel
     *
     * @return object|null
     */
    static function
    getWideImage(
        stdClass $viewModel
    ): ?stdClass
    {
        return CBModel::valueAsModel(
            $viewModel,
            'CB_CBView_Hero1_wideImage_property',
            'CBImage'
        );
    }
    // getWideImage()



    /**
     * @param object $viewModel
     * @param object|null $newWideImageModel
     *
     * @return void
     */
    static function
    setWideImage(
        stdClass $viewModel,
        ?stdClass $newWideImageModel
    ): void
    {
        $viewModel->CB_CBView_Hero1_wideImage_property =
        $newWideImageModel;
    }
    // setWideImage()

}
