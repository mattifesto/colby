<?php

/**
 * This view is added to each product page create for a product that should have
 * a page by the SCProductUpdateTask class.
 *
 * This view will only render images if there are non-empty artworks contained
 * in the CBArtworkCollection associated with this product. It will not display
 * an image associated using the CBModelToCBImageAssociation association key,
 * which is being deprecated.
 */
final class SCProduct_ProductPageMainView {

    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        $productCode = CBModel::valueAsName(
            $viewSpec,
            'productCode'
        );

        return (object)[
            'productCode' => $productCode,
        ];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return object
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $hideProductViewImage = false;

        $productCode = CBModel::valueAsName(
            $viewModel,
            'productCode'
        );

        if ($productCode === null) {
            return;
        }

        ?>

        <div class="SCProduct_ProductPageMainView">

            <?php

            CBView::renderSpec(
                (object)[
                    'className' => 'SCProductBuyView',
                    'hideImage' => true,
                    'productCode' => $productCode,
                ]
            );

            $artworkCollectionModel = CBModelCache::fetchModelByID(
                SCProduct::productCBIDToArtworkCollectionCBID(
                    SCProduct::productCodeToProductID(
                        $productCode
                    )
                )
            );

            if ($artworkCollectionModel !== null) {
                CBView::render(
                    (object)[
                        'className' => 'CBArtworkCollectionView',
                        'artworkCollection' => $artworkCollectionModel,
                        'CSSClassNames' => [
                            'CBArtworkCollectionView_theme1',
                        ],
                    ]
                );
            }

            ?>

        </div>

        <?php
    }
    /* CBView_render() */

}
