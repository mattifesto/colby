<?php

final class
CB_View_TagStickerBar
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v2022.05.23.1653328157.css',
                cbsysurl()
            ),
        ];
    }
    // CBHTMLOutput_CSSURLs()



    // -- CBModel interfaces



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

        CB_View_TagStickerBar::setPrettyTagNames(
            $viewModel,
            CB_View_TagStickerBar::getPrettyTagNames(
                $viewSpec
            )
        );

        return
        $viewModel;
    }
    // CBModel_build()



    // -- CBView interfaces



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
        $prettyTagNames =
        CB_View_TagStickerBar::getPrettyTagNames(
            $viewModel
        );

        ?>

        <div class="CB_View_TagStickerBar_root_element">

        <?php

        foreach (
            $prettyTagNames as $prettyTagName
        ) {
            $imageModel =
            CB_Tag::fetchAndCacheAssociatedImageModelByPrettyTagName(
                $prettyTagName
            );

            if (
                $imageModel !== null
            ) {
                CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
                    $imageModel,
                    'rl320',
                    200,
                    25,
                    $prettyTagName
                );
            }
        }

        ?>

        </div>

        <?php
    }
    // CBView_render()



    // -- accessors



    /**
     * @param object $viewModel
     *
     * @return [string]
     */
    static function
    getPrettyTagNames(
        stdClass $viewModel
    ): array
    {
        $prettyTagNames =
        CBModel::valueToArray(
            $viewModel,
            'CB_View_TagStickerBar_prettyTagNames_property'
        );

        return
        $prettyTagNames;
    }
    // getPrettyTagNames()



    /**
     * @param object $viewModel
     * @param [string] $prettyTagNames
     *
     * @return void
     */
    static function
    setPrettyTagNames(
        stdClass $viewModel,
        array $newPrettyTagNames
    ): void
    {
        $viewModel->CB_View_TagStickerBar_prettyTagNames_property =
        $newPrettyTagNames;
    }
    // setPrettyTagNames()

}
