<?php

final class SCProductGroupUpdateTask {

    /**
     * @param string $productGroupID
     *
     * @return null
     */
    static function CBTasks2_run($productGroupID): ?stdClass {
        $productGroupModel = CBModelCache::fetchModelByID($productGroupID);

        if ($productGroupModel === null) {
            SCProductGroupUpdateTask::removeProductGroupPage(
                $productGroupID
            );

            SCProductGroupUpdateTask::removeProductGroupAssociations(
                $productGroupID
            );
        } else {
            if ($productGroupModel->className !== 'SCProductGroup') {
                throw CBException::createModelIssueException(
                    'This model is not an SCProductGroup model.',
                    $productModel,
                    'f3dff3ebd39a43b114ee21a25d1087d963732935'
                );
            }

            SCProductGroupUpdateTask::updateProductGroupPage(
                $productGroupModel
            );

            SCProductGroupUpdateTask::updateProductAssociations(
                $productGroupModel
            );
        }


        /* -- finished -- -- -- -- -- */

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
        return 80;
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
    private static function removeProductGroupPage(
        string $productGroupID
    ): void {
        $productGroupPageID =
        SCProductGroup::productGroupIDToProductGroupPageID(
            $productGroupID
        );

        CBModelUpdater::updateIfExists(
            (object)[
                'ID' => $productGroupPageID,
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
    private static function removeProductGroupAssociations(
        string $productGroupID
    ): void {
        CBModelAssociations::delete($productGroupID);
        CBModelAssociations::delete(null, null, $productGroupID);
    }
    /* removeProductAssociations() */


    /**
     * @param object $productGroupModel
     *
     * @return void
     */
    private static function updateProductAssociations(
        stdClass $productGroupModel
    ): void {
        /* -- initial products -- -- -- -- -- */

        $associations = CBModelAssociations::fetch(
            $productGroupModel->ID,
            'SCProductGroup_product'
        );

        $initialProductIDs = array_map(
            function ($association) {
                return $association->associatedID;
            },
            $associations
        );


        /* -- final products -- -- -- -- -- */

        $associations = CBModelAssociations::fetch(
            null,
            'SCProduct_group',
            $productGroupModel->ID
        );

        $finalProductIDs = array_map(
            function ($association) {
                return $association->ID;
            },
            $associations
        );


        /* -- remove products -- -- -- -- -- */

        $removedProductIDs = array_diff(
            $initialProductIDs,
            $finalProductIDs
        );

        foreach ($removedProductIDs as $removedProductID) {
            CBModelAssociations::delete(
                $productGroupModel->ID,
                'SCProductGroup_product',
                $removedProductID
            );
        }


        /* -- added products -- -- -- -- -- */

        $addedProductIDs = array_diff(
            $finalProductIDs,
            $initialProductIDs
        );

        foreach ($addedProductIDs as $addedProductID) {
            CBModelAssociations::add(
                $productGroupModel->ID,
                'SCProductGroup_product',
                $addedProductID
            );
        }
    }
    /* updateProductAssociations() */


    /**
     * @param object $productGroupModel
     *
     * @return void
     */
    private static function updateProductGroupPage(
        stdClass $productGroupModel
    ): void {
        if (!CBModel::valueToBool($productGroupModel, 'hasPage')) {
            SCProductGroupUpdateTask::removeProductGroupPage(
                $productGroupModel->ID
            );

            return;
        }

        $productGroupName = CBModel::valueAsName(
            $productGroupModel,
            'name'
        );

        if ($productGroupName === null) {
            throw CBException::createModelIssueException(
                'This model has an invalid "name" property value.',
                $productGroupModel,
                'e94496560ac16dce163001172e1ef807f528d60d'
            );
        }

        $productGroupPageID =
        SCProductGroup::productGroupIDToProductGroupPageID(
            $productGroupModel->ID
        );

        $updater = CBModelUpdater::fetch(
            CBModelTemplateCatalog::fetchLivePageTemplate(
                (object)[
                    'ID' => $productGroupPageID,

                    'classNameForKind' => 'SCGeneratedProductGroupPageKind',

                    'isPublished' => true,

                    'title' => CBModel::valueToString(
                        $productGroupModel,
                        'title'
                    ),

                    'URI' => (
                        SCProductGroup::productGroupNameToProductGroupPageURI(
                            $productGroupName
                        )
                    ),
                ]
            )
        );

        $pageSpec = $updater->working;

        CBSubviewUpdater::unshift(
            $pageSpec,
            'sourceID',
            'dbd20e89758dd8038ac35edef5d6ee54cb9c37cf',
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
                'sourceID' => 'dbd20e89758dd8038ac35edef5d6ee54cb9c37cf',
            ]
        );

        CBSubviewUpdater::push(
            $pageSpec,
            'sourceID',
            '7a15e17eafd97ab31220ca22a043932e65c392a3',
            (object)[
                'className' => 'SCProductGroupView',
                'productGroupName' => $productGroupName,
                'sourceID' => '7a15e17eafd97ab31220ca22a043932e65c392a3'
            ]
        );

        CBModelUpdater::save($updater);
    }
    /* updateProductGroupPage() */
}
