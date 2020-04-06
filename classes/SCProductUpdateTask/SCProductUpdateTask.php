<?php

/**
 * This task is restarted every time an SCProduct model is saved or deleted.
 *
 * It will perform the following actions:
 *
 *      Update the product page.
 *
 *      Update the product group associations.
 */
final class SCProductUpdateTask {

    /**
     * @param string $productID
     *
     * @return null
     */
    static function CBTasks2_run(string $productID): ?stdClass {
        $productModel = CBModelCache::fetchModelByID($productID);

        if ($productModel === null) {
            SCProductUpdateTask::removeProductPage($productID);
            SCProductUpdateTask::removeProductAssociations($productID);
        } else {
            if ($productModel->className !== 'SCProduct') {
                throw CBException::createModelIssueException(
                    'This model is not an SCProduct model.',
                    $productModel,
                    '1b6bae19a2a4437c69fea3962f3c3a16952da931'
                );
            }

            SCProductUpdateTask::updateProductPage($productModel);
            SCProductUpdateTask::updateProductGroups($productModel);
        }

        return null;
    }
    /* CBTasks2_run() */


    /* -- functions -- -- -- -- -- */

    /**
     * @return int
     *
     *      SCProductUpdateTask has a default priority of 70.
     *      SCProductGroupUpdateTask has a default priority of 80.
     *
     *      This ensures products are always updated before product groups.
     */
    static function getDefaultPriority(): int {
        return 70;
    }


    /**
     * This function will just unplublish the page instead of deleting it
     * because users may have design elements save with the page that they don't
     * want to lose.
     *
     * @param string $productID
     *
     * @return void
     */
    private static function removeProductPage(
        string $productID
    ): void {
        $productPageID = SCProduct::productIDToProductPageID($productID);

        CBModelUpdater::updateIfExists(
            (object)[
                'ID' => $productPageID,

                'isPublished' => false,
            ]
        );
    }
    /* removeProductPage() */


    /**
     * @param string $productID
     *
     * @return void
     */
    private static function removeProductAssociations(
        string $productID
    ): void {
        CBModelAssociations::delete($productID);
        CBModelAssociations::delete(null, null, $productID);
    }
    /* removeProductAssociations() */


    /**
     * @param object $productModel
     *
     * @return void
     */
    private static function updateProductGroups(
        stdClass $productModel
    ): void {
        /* -- initial product groups -- -- -- -- -- */

        $associations = CBModelAssociations::fetch(
            $productModel->ID,
            'SCProduct_group'
        );

        $initialProductGroupIDs = array_map(
            function ($association) {
                return $association->associatedID;
            },
            $associations
        );


        /* -- final product groups -- -- -- -- -- */

        $productGroupNames = CBModel::valueToArray(
            $productModel,
            'groupNames'
        );

        $finalProductGroupIDs = array_map(
            function ($productGroupName) {
                return SCProductGroup::productGroupNameToProductGroupID(
                    $productGroupName
                );
            },
            $productGroupNames
        );


        /* -- removed product groups -- -- -- -- -- */

        $removedProductGroupIDs = array_diff(
            $initialProductGroupIDs,
            $finalProductGroupIDs
        );

        foreach($removedProductGroupIDs as $removedProductGroupID) {
            CBModelAssociations::delete(
                $productModel->ID,
                'SCProduct_group',
                $removedProductGroupID
            );

            CBTasks2::restart(
                'SCProductGroupUpdateTask',
                $removedProductGroupID,
                SCProductGroupUpdateTask::getDefaultPriority()
            );
        }


        /* -- added product groups -- -- -- -- -- */

        $addedProductGroupIDs = array_diff(
            $finalProductGroupIDs,
            $initialProductGroupIDs
        );

        foreach ($addedProductGroupIDs as $addedProductGroupID) {
            CBModelAssociations::add(
                $productModel->ID,
                'SCProduct_group',
                $addedProductGroupID
            );

            CBTasks2::restart(
                'SCProductGroupUpdateTask',
                $addedProductGroupID,
                SCProductGroupUpdateTask::getDefaultPriority()
            );
        }
    }
    /* updateProductGroups() */


    /**
     * @param object $productModel
     *
     * @return void
     */
    private static function updateProductPage(
        stdClass $productModel
    ): void {
        if (!CBModel::valueToBool($productModel, 'hasPage')) {
            SCProductUpdateTask::removeProductPage($productModel->ID);

            return;
        }

        $productCode = CBModel::valueAsName(
            $productModel,
            'productCode'
        );

        if ($productCode === null) {
            throw CBException::createModelIssueException(
                'This model has an invalid "productCode" property value.',
                $productModel,
                '09d957d774e9b9d2f1e7211f089f8468ade419fd'
            );
        }

        $productPageID = SCProduct::productIDToProductPageID(
            $productModel->ID
        );

        $updater = CBModelUpdater::fetch(
            CBModelTemplateCatalog::fetchLivePageTemplate(
                (object)[
                    'ID' => $productPageID,

                    'classNameForKind' => 'SCGeneratedProductPageKind',

                    'isPublished' => true,

                    'title' => CBModel::valueToString(
                        $productModel,
                        'title'
                    ),

                    'URI' => SCProduct::productCodeToProductPageURL(
                        $productCode
                    ),
                ]
            )
        );

        $pageSpec = $updater->working;

        CBSubviewUpdater::unshift(
            $pageSpec,
            'sourceID',
            '36eb61c842301fbafec147aaf918b9a801159a32',
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
                'sourceID' => '36eb61c842301fbafec147aaf918b9a801159a32',
            ]
        );

        CBSubviewUpdater::push(
            $pageSpec,
            'sourceID',
            'afe00a7ebf6f609782d6112700b34d9d4b011cae',
            (object)[
                'className' => 'SCProductBuyView',
                'productCode' => $productCode,
                'sourceID' => 'afe00a7ebf6f609782d6112700b34d9d4b011cae'
            ]
        );

        CBModelUpdater::save($updater);
    }
    /* updateProductPage() */
}
