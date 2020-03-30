<?php

final class SCProductGroupView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v123.css', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[
            'productGroupName' => CBModel::valueAsName(
                $spec,
                'productGroupName'
            ),
        ];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $productGroupName = CBModel::valueAsName(
            $model,
            'productGroupName'
        );

        if ($productGroupName === null) {
            return;
        }

        echo '<div class="SCProductGroupView CBUI_container2">';

        $associations = CBModelAssociations::fetch(
            SCProductGroup::productGroupNameToProductGroupID(
                $productGroupName
            ),
            'SCProductGroup_product'
        );

        $productIDs = array_map(
            function ($association) {
                return $association->associatedID;
            },
            $associations
        );

        $productModels = CBModelCache::fetchModelsByID($productIDs);

        foreach ($productModels as $productModel) {
            CBView::render(
                (object)[
                    'className' => 'SCProductBuyView',

                    'productCode' => CBModel::valueAsName(
                        $productModel,
                        'productCode'
                    ),

                    'showProductPageLink' => CBModel::valueToBool(
                        $productModel,
                        'hasPage'
                    ),
                ]
            );
        }

        echo '</div>';
    }
    /* CBView_render() */

}
