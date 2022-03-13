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
                'v675.61.3.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



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
        $viewModel = (object)[];

        CB_CBView_Hero1::setAlternativeText(
            $viewModel,
            CB_CBView_Hero1::getAlternativeText(
                $viewSpec
            )
        );

        CB_CBView_Hero1::setNarrowImage(
            $viewModel,
            CB_CBView_Hero1::getNarrowImage(
                $viewSpec
            )
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
        '<div class="CB_CBView_Hero1_root_element">';

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

        echo
        '</div>';
    }
    /* CBView_render() */



    /* -- accessors -- */



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
