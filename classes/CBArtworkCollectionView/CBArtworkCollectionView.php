<?php

/**
 * @NOTE 2022_03_02
 *
 *      This is an odd view because it doesn't implement the CBModel interfaces.
 *      If a view is never saved as a model, this is okay, but in the future the
 *      CBModel interfaces should be implemented or this should move away from
 *      being a "view" and just be a class that has a function that renders
 *      HTML.
 */
final class
CBArtworkCollectionView
{
    /* -- CBAdmin_CBDocumentationForClass interfaces -- */



    /**
     * @return void
     */
    static function
    CBAdmin_CBDocumentationForClass_render(
    ): void
    {
        CBHTMLOutput::requireClassName(
            'CB_Documentation_ArtworkCollectionView'
        );

        $imageModels = CBModels::fetchRandomModelsByClassName(
            'CBImage',
            10
        );

        $artworkCollectionViewModel = CBModel::createSpec(
            'CBArtworkCollectionView'
        );

        CBArtworkCollectionView::setArtworkCollection(
            $artworkCollectionViewModel,
            CBArtworkCollection::fromImageSpecs(
                $imageModels
            )
        );

        CBView::render(
            $artworkCollectionViewModel
        );
    }
    /* CBAdmin_CBDocumentationForClass_render() */



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_09_1665278245',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_09_1665278268',
                'js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $arrayOfRequiredClassNames =
        [
            'CBArtwork',
            'CBImage',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     *      {
     *          artworkCollection: object
     *          CSSClassNames: [string]
     *      }
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void
    {
        $artworkCollectionModel = CBArtworkCollectionView::getArtworkCollection(
            $viewModel
        );

        $artworks = CBArtworkCollection::getArtworks(
            $artworkCollectionModel
        );

        if (
            count($artworks) === 0
        ) {
            return;
        }

        $artworksAsData = cbhtml(
            json_encode(
                $artworks
            )
        );


        /* CSS Class Names */

        $CSSClassNames = CBModel::valueToArray(
            $viewModel,
            'CSSClassNames'
        );

        array_walk(
            $CSSClassNames,
            function (
                $CSSClassName
            ) {
                CBHTMLOutput::requireClassName(
                    $CSSClassName
                );
            }
        );

        $CSSClassNames = cbhtml(
            implode(
                ' ',
                $CSSClassNames
            )
        );


        /* HTML */

        /**
         * @NOTE 2022_10_09_1665277753
         *
         *      The CBArtworkCollectionView_mainPictureContainer_element was
         *      created and rendered by JavaScript before changes made today.
         *      Rendering this element in HTML along with giving it an aspect
         *      ratio and other properties in CSS allows this view to be mostly
         *      laid out properly immediately. That is, rendering this element
         *      from PHP prevents a "cumulative layout shift" that Google will
         *      notice.
         *
         *      In theory, we could do the same for the thumbnail images but
         *      haven't yet because they don't cause as much of a shift.
         */

        ?>

        <div
            class="CBArtworkCollectionView_root_element <?= $CSSClassNames ?>"
            data-artworks="<?= $artworksAsData ?>"
        >

            <div
                class="CBArtworkCollectionView_content_element"
            >

                <div
                    class="CBArtworkCollectionView_mainPictureContainer_element"
                >
                </div>

                <div
                    class="CBArtworkCollectionView_thumbnailsContainer_element"
                >

                    <?php

                    foreach (
                        $artworks
                        as $loopArtworkModel
                    ) {
                        $imageModelOrURL =
                        CBArtwork::getImageModel(
                            $loopArtworkModel
                        );

                        if (
                            $imageModelOrURL ===
                            null
                        ) {
                            $imageModelOrURL =
                            CBArtwork::getThumbnailImageURL(
                                $loopArtworkModel
                            );
                        }

                        $alternativeText =
                        "Thumbnail";
                        
                        CBImage::renderPictureElementWithImageInsideAspectRatioBox(
                            $imageModelOrURL,
                            'rw320',
                            75,
                            75,
                            $alternativeText
                        );
                    }

                    ?>

                </div>

            </div>

        </div>

        <?php
    }
    /* CBView_render() */



    /* -- accessors -- */


    /**
     * @param object $artworkCollectionViewModel
     *
     * @return object|null
     */
    static function
    getArtworkCollection(
        stdClass $artworkCollectionViewModel
    ): ?stdClass
    {
        return CBModel::valueAsModel(
            $artworkCollectionViewModel,
            'artworkCollection',
            'CBArtworkCollection'
        );
    }
    /* getArtworkCollection() */



    /**
     * @param object $artworkCollectionViewModel
     * @param object $artworkCollectionModel
     *
     * @return void
     */
    static function
    setArtworkCollection(
        stdClass $artworkCollectionViewModel,
        stdClass $artworkCollectionModel
    ): void
    {
        $artworkCollectionViewModel->artworkCollection = (
            $artworkCollectionModel
        );
    }
    /* setArtworkCollection() */

}
