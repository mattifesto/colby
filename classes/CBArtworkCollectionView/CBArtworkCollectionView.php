<?php

final class CBArtworkCollectionView {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v632.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v632.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBArtwork',
            'CBArtworkElement',
            'CBUI',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $artworks = CBModel::valueToArray(
            $viewModel,
            'artworkCollection.artworks'
        );

        $artworksAsData = cbhtml(
            json_encode(
                $artworks
            )
        );

        ?>

        <div
            class="CBArtworkCollectionView"
            data-artworks="<?= $artworksAsData ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */

}
