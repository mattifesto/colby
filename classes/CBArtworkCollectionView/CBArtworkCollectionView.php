<?php

final class
CBArtworkCollectionView
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
                'v675.60.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.60.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
            'CBArtwork',
            'CBArtworkElement',
            'CBJavaScript',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



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

        ?>

        <div
            class="CBArtworkCollectionView_root_element <?= $CSSClassNames ?>"
            data-artworks="<?= $artworksAsData ?>"
        >
            <div class="CBArtworkCollectionView_content_element">
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
