<?php

final class
CBLinkView1
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $cssURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_09_10_1662826792',
                'css',
                cbsysurl()
            ),
        ];

        return $cssURLs;
    }
    // CBHTMLOutput_CSSURLs()



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
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
    ): array
    {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



    // -- CBModel interfaces



    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass
    {
        $model =
        (object)
        [
            'description' =>
            CBModel::valueToString(
                $spec,
                'description'
            ),

            'size' =>
            trim(
                CBModel::valueToString(
                    $spec,
                    'size'
                )
            ),

            'URL' =>
            trim(
                CBModel::valueToString(
                    $spec,
                    'URL'
                )
            ),
        ];

        /* image */

        $imageSpec =
        CBModel::valueAsModel(
            $spec,
            'image',
            'CBImage'
        );

        if (
            $imageSpec !==
            null
        ) {
            $imageModel =
            CBModel::build(
                $imageSpec
            );

            $model->image =
            $imageModel;
        }

        return $model;
    }
    // CBModel_build()



    /**
     * @param model $spec
     *
     * @return model
     */
    static function
    CBModel_upgrade(
        stdClass $spec
    ): stdClass
    {
        if (
            $imageSpec =
            CBModel::valueAsObject(
                $spec,
                'image'
            )
        ) {
            $spec->image =
            CBImage::fixAndUpgrade(
                $imageSpec
            );
        }

        return $spec;
    }
    // CBModel_upgrade()



    /**
     * @param object $model
     *
     * @return null
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void
    {
        $title =
        trim(
            CBModel::valueToString(
                $viewModel,
                'title'
            )
        );

        $description =
        trim(
            CBModel::valueToString(
                $viewModel,
                'description'
            )
        );

        $imageModel =
        CBModel::valueAsModel(
            $viewModel,
            'image',
            'CBImage'
        );



        $thereIsNothingToRender =
        $title === '' &&
        $description === '' &&
        $imageModel === null;

        if (
            $thereIsNothingToRender
        ) {
            return;
        }



        $size =
        CBModel::value(
            $viewModel,
            'size'
        );

        $URL =
        CBModel::value(
            $viewModel,
            'URL',
            ''
        );

        switch (
            $size
        ) {
            case 'small':

            $maximumDisplayWidthInCSSPixels =
            240;

            $imageResizeOperation =
            'rw480';

            break;



            case 'large':

            $maximumDisplayWidthInCSSPixels =
            480;

            $imageResizeOperation =
            'rw960';

            break;



            default:

            $size =
            'medium';

            $maximumDisplayWidthInCSSPixels =
            320;

            $imageResizeOperation =
            'rw640';

            break;
        }

        ?>

        <figure class="CBLinkView1_root_element <?= $size ?>">

                <a
                    class="CBLinkView1_content_element"
                    href="<?= cbhtml($URL) ?>">

                    <?php

                    if (
                        $imageModel !==
                        null
                    ) {
                        $maximumDisplayHeightInCSSPixels =
                        $maximumDisplayWidthInCSSPixels *
                        3;

                        $alternativeText =
                        $title;

                        CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight(
                            $imageModel,
                            $imageResizeOperation,
                            $maximumDisplayWidthInCSSPixels,
                            $maximumDisplayHeightInCSSPixels,
                            $alternativeText
                        );
                    }

                    ?>

                    <figcaption>
                        <div class="CBLinkView1_title_element"><?=
                            cbhtml($title)
                        ?></div>
                        <div class="CBLinkView1_description_element"><?=
                            cbhtml($description)
                        ?></div>
                    </figcaption>
                </a>

        </figure>

        <?php
    }
    // CBView_render()

}
