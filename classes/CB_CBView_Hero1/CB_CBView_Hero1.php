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
                'v675.61.2.css',
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
        $wideImage =
        CB_CBView_Hero1::getWideImage(
            $viewModel
        );

        if (
            $wideImage === null
        ) {
            return;
        }

        echo
        '<div class="CB_CBView_Hero1_root_element">';

        CBImage::renderPictureElementWithSize(
            $wideImage,
            'rw2560',
            CBImage::getOriginalWidth(
                $wideImage
            ),
            CBImage::getOriginalHeight(
                $wideImage
            )
        );

        echo
        '</div>';
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @oaram object $viewModel
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
