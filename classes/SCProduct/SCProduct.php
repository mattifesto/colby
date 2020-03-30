<?php

final class SCProduct {

    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $productCode = CBModel::valueAsName($spec, 'productCode');

        if ($productCode === null) {
            throw CBException::createModelIssueException(
                'This spec has an invalid "productCode" property value.',
                $spec,
                '6b48723103a735d962ffd3cbc7f9066f9c214820'
            );
        }

        $priceInCents = CBModel::valueAsInt($spec, 'priceInCents');

        if ($priceInCents === null || $priceInCents < 0) {
            throw CBException::createModelIssueException(
                'This spec has an invalid "priceInCents" property value.',
                $spec,
                'fe0d4abaf03547e4acd4925b8981c2bb588180e6'
            );
        }

        $productGroupNames = CBModel::valueAsNames(
            $spec,
            'groupNames'
        );

        if ($productGroupNames === null) {
            throw CBException::createModelIssueException(
                'This spec has an invalid "groupNames" property value.',
                $spec,
                'df5aaa40a8da5338b4d21a086019dbf6e704dca6'
            );
        }

        return (object)[
            'groupNames' => $productGroupNames,

            'hasPage' => CBModel::valueToBool(
                $spec,
                'hasPage'
            ),

            'isNotAvailable' => CBModel::valueToBool(
                $spec,
                'isNotAvailable'
            ),

            'priceInCents' => $priceInCents,

            'productCode' => $productCode,

            'title' => trim(
                CBModel::valueToString(
                    $spec,
                    'title'
                )
            ),
        ];
    }
    /* CBModel_build() */


    /**
     * @param object $spec
     *
     * @return string
     */
    static function CBModel_toID(stdClass $spec): string {
        $productCode = CBModel::valueAsName($spec, 'productCode');

        if ($productCode === null) {
            throw CBException::createModelIssueException(
                'This spec has an invalid "productCode" property value.',
                $spec,
                '9aef7547feef7ff5e196feb4d85d41d4e1a5c8d9'
            );
        }

        return SCProduct::productCodeToProductID($productCode);
    }
    /* CBModel_toID() */


    /* -- CBModels interfaces -- -- -- -- -- */

    /**
     * @param [string] $IDs
     *
     * @return void
     */
    static function CBModels_willDelete($IDs): void {
        CBTasks2::restart(
            'SCProductUpdateTask',
            $IDs,
            SCProductUpdateTask::getDefaultPriority()
        );
    }
    /* CBModels_willDelete() */


    /**
     * @param [object] $models
     *
     * @return void
     */
    static function CBModels_willSave(array $models): void {
        $IDs = array_map(
            function ($model) {
                return $model->ID;
            },
            $models
        );

        CBTasks2::restart(
            'SCProductUpdateTask',
            $IDs,
            SCProductUpdateTask::getDefaultPriority()
        );
    }
    /* CBModels_willSave() */


    /* -- functions -- -- -- -- -- */

    /**
     * Generate an ID for an SCProduct model.
     *
     * @param string $productCode
     *
     * @return ID
     */
    static function productCodeToProductID(string $productCode): string {
        return sha1("f28573d30b15a24d71f5d0af23624d8b0029d5d4 {$productCode}");
    }


    /**
     * Generate an ID for a product CBViewPage model.
     *
     * @param string $productID
     *
     *      This function uses the product ID as its parameter instead of the
     *      product code so that the product page ID can be determined even if
     *      the product model has been deleted and we no longer have access to
     *      the product code.
     *
     * @return string
     */
    static function productIDToProductPageID(string $productID): string {
        return sha1("13ef22a79c5a4583b1199d31201925af8cb51bf8 {$productID}");
    }


    /**
     * @param string $productCode
     *
     * @return string
     */
    static function productCodeToProductPageURL(string $productCode): string {
        return (
            '/products/' .
            CBConvert::stringToStub($productCode) .
            '/'
        );
    }
}
