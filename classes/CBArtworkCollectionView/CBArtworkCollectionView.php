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
            Colby::flexpath(__CLASS__, 'v637.js', cbsysurl()),
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
     *      {
     *          artworkCollection: object
     *          CSSClassNames: [string]
     *      }
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

        if (count($artworks) === 0) {
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
            function ($CSSClassName) {
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
            class="CBArtworkCollectionView CBUI_view <?= $CSSClassNames ?>"
            data-artworks="<?= $artworksAsData ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */

}
