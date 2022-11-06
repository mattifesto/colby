<?php

final class
SCProduct
{
    /* -- CBArtworkCollection interfaces -- */



    /**
     * @return void
     */
    static function CBArtworkCollection_UpdatedTask_notify(
        stdClass $productModel
    ): void {
        $artworkCollectionCBID = SCProduct::productCBIDToArtworkCollectionCBID(
            $productModel->ID
        );

        $artworkCollectionModel = CBModelCache::fetchModelByID(
            $artworkCollectionCBID
        );

        if ($artworkCollectionModel === null) {
            return;
        }

        $mainArtworModel = CBArtworkCollection::getMainArtwork(
            $artworkCollectionModel
        );

        if ($mainArtworModel === null) {
            return;
        }

        $mainArtworkImageModel = CBArtwork::getImageModel(
            $mainArtworModel
        );

        if ($mainArtworkImageModel === null) {
            return;
        }

        $associationKey = 'CBModelToCBImageAssociation';

        CBDB::transaction(
            function () use (
                $productModel,
                $associationKey,
                $mainArtworkImageModel
            ) {
                CBModelAssociations::replaceAssociatedID(
                    $productModel->ID,
                    $associationKey,
                    $mainArtworkImageModel->ID
                );

                SCProduct::updateProductPageThumbnail(
                    $productModel,
                    $mainArtworkImageModel
                );
            }
        );
    }
    /* CBArtworkCollection_UpdatedTask_notify() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        $contentCBMessage = CBModel::valueToString(
            $spec,
            'contentCBMessage'
        );

        $productCode =
        SCProduct::getProductCode(
            $spec
        );

        if (
            $productCode ===
            ''
        ) {
            throw CBException::createModelIssueException(
                'This spec has an invalid "productCode" property value.',
                $spec,
                '6b48723103a735d962ffd3cbc7f9066f9c214820'
            );
        }

        $priceInCents = CBModel::valueAsInt(
            $spec,
            'priceInCents'
        );

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
            'contentCBMessage' => $contentCBMessage,

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
     * @param <SCProduct model> $productModelArgument
     *
     * @return string
     */
    static function
    CBModel_getAbsoluteURLPath(
        stdClass $productModelArgument
    ): string
    {
        $absoluteURLPath =
        '';

        $productCode =
        SCProduct::getProductCode(
            $productModelArgument
        );

        if (
            $productCode !==
            ''
        ) {
            $absoluteURLPath =
            SCProduct::productCodeToProductPageURL(
                $productCode
            );
        }

        return $absoluteURLPath;
    }
    // CBModel_getAbsoluteURLPath()



    /**
     * @param <SCProduct model> $productModelArgument
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $productModelArgument
    ): string
    {
        $productTitle =
        SCProduct::getTitle(
            $productModelArgument
        );

        return $productTitle;
    }
    // CBModel_getTitle()



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



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_upgrade(
        stdClass $spec
    ): stdClass {

        /**
         * @NOTE 2020_09_06
         *
         *      The processVersionNumber is set to 2 on the spec to force a
         *      re-save because product pages need to be upgraded.
         */
        $spec->processVersionNumber = 2;

        return $spec;
    }
    /* CBModel_upgrade() */



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



    // -- accessors



    /**
     * @param <SCProduct model> $productModelArgument
     *
     * @return string
     *
     *      Returns an empty string if the product does not have a valid product
     *      code.
     */
    static function
    getProductCode(
        stdClass $productModelArgument
    ): string
    {
        $productCode =
        CBModel::valueAsName(
            $productModelArgument,
            'productCode'
        );

        if (
            $productCode ===
            null
        ) {
            $productCode =
            '';
        }

        return $productCode;
    }
    // getProductCode()



    /**
     * @param <SCProduct model> $productModelArgument
     *
     * @return string
     */
    static function
    getTitle(
        stdClass $productModelArgument
    ): string
    {
        $productTitle =
        CBModel::valueToString(
            $productModelArgument,
            'title'
        );

        $productTitle =
        trim(
            $productTitle
        );

        return $productTitle;
    }
    // getTitle()



    // -- functions



    /**
     * @deprecated 2022_10_09_1665335161
     *
     *      This function was created in a deprecated state. It exists for
     *      products that can provide thumbnail image URLs but that don't have a
     *      product image model. Use cases for this have existed in the past and
     *      are being removed but new uses of this should not be created.
     *
     *      Products should be associated with CBImage models.
     *
     * @param object $productModelArgument
     *
     * @return string
     *
     *      Returns an empty string if the product does not have a thumbnail
     *      image URl associated with it.
     */
    static function
    getThumbnailImageURL(
        stdClass $productModelArgument
    ): string
    {
        $callable =
        CBModel::getClassFunction(
            $productModelArgument,
            'SCProduct_getThumbnailImageURL'
        );

        if (
            $callable ===
            null
        ) {
            return '';
        }

        $thumbnailImageURL =
        call_user_func(
            $callable,
            $productModelArgument
        );

        return $thumbnailImageURL;
    }
    // getThumbnailImageURL()



    /**
     * This function can also be used for product models that have a class name
     * other than SCProduct.
     *
     * @param string productCBID
     *
     * @return string
     */
    static function
    productCBIDToArtworkCollectionCBID(
        string $productCBID
    ): string
    {
        if (
            !CBID::valueIsCBID(
                $productCBID
            )
        ) {
            $message =
            "This value is not a CBID";

            $value =
            $productCBID;

            $sourceCBID =
            "bd48cb576b59cffc049067eee24522596ca46587";

            throw new CBExceptionWithValue(
                $message,
                $value,
                $sourceCBID
            );
        }

        return sha1(
            "d45dc86e5de33cda02bff8b69c1f079536dcddf3 {$productCBID}"
        );
    }
    /* productCBIDToArtworkCollectionCBID() */



    /**
     * @deprecated 2022_10_09_1665335804
     *
     *      This function finds the best possible thumbnail URL for a product.
     *      It's deprecated because in the future we will only use CBImage
     *      models for product images and this function removes the CBImage
     *      model from use. This is bad because it prevents user of generated
     *      images like webp.
     *
     * @param CBID $productCBID
     *
     * @param string
     *
     *      Returns the thumbnail image URL for the main image for the product.
     *      May return an empty string if there is no image for the product or
     *      if the product doesn't exist.
     */
    static function
    productCBIDToThumbnailImageURL(
        string $productModelCBIDArgument
    ): string
    {
        $mainProductImageModel =
        SCProduct::productModelCBIDToMainImageModel(
            $productModelCBIDArgument
        );

        if (
            $mainProductImageModel !==
            null
        ) {
            $thumbnailImageURL =
            CBImage::asFlexpath(
                $mainProductImageModel,
                'rl640',
                cbsiteurl()
            );

            return $thumbnailImageURL;
        }

        $productModel =
        CBModelCache::fetchModelByID(
            $productModelCBIDArgument
        );

        if (
            $productModel ===
            null
        ) {
            return '';
        }

        $thumbnailImageURL =
        SCProduct::getThumbnailImageURL(
            $productModel
        );

        return $thumbnailImageURL;
    }
    /* productCBIDToThumbnailImageURL() */



    /**
     * Generate an ID for an SCProduct model.
     *
     * @param string $productCode
     *
     * @return ID
     */
    static function productCodeToProductID(
        string $productCode
    ): string {
        return sha1(
            "f28573d30b15a24d71f5d0af23624d8b0029d5d4 {$productCode}"
        );
    }
    /* productCodeToProductID() */



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
    static function productIDToProductPageID(
        string $productID
    ): string {
        return sha1(
            "13ef22a79c5a4583b1199d31201925af8cb51bf8 {$productID}"
        );
    }
    /* productIDToProductPageID() */



    /**
     * @param string $productCode
     *
     * @return string
     */
    static function
    productCodeToProductPageURI(
        string $productCode
    ): string {
        return (
            'products/' .
            CBConvert::stringToStub(
                $productCode
            )
        );
    }
    /* productCodeToProductPageURI() */



    /**
     * @param string $productCode
     *
     * @return string
     */
    static function
    productCodeToProductPageURL(
        string $productCode
    ): string {
        $productPageURI = SCProduct::productCodeToProductPageURI(
            $productCode
        );

        return "/{$productPageURI}/";
    }
    /* productCodeToProductPageURL() */



    /**
     * @param CBID $productModelCBIDArgument
     *
     * @return <CBImage model>|null
     */
    static function
    productModelCBIDToMainImageModel(
        $productModelCBIDArgument
    ): ?stdClass
    {
        $productArtworkCollectionCBID =
        SCProduct::productCBIDToArtworkCollectionCBID(
            $productModelCBIDArgument
        );

        $productArtworkCollectionModel =
        CBModelCache::fetchModelByCBID(
            $productArtworkCollectionCBID
        );

        if (
            $productArtworkCollectionModel ===
            null
        ) {
            return null;
        }

        $productMainArtworkModel =
        CBArtworkCollection::getMainArtwork(
            $productArtworkCollectionModel
        );

        if (
            $productMainArtworkModel ===
            null
        ) {
            return null;
        }

        $mainImageModel =
        CBArtwork::getImageModel(
            $productMainArtworkModel
        );

        return $mainImageModel;
    }
    // productModelCBIDToMainImageModel();



    /**
     * You must call this function inside a database transaction.
     *
     * @param object $productModel
     *
     *      This is the model for the product, not the spec.
     *
     * @param object $imageModel
     *
     * @return void
     */
    private static function
    updateProductPageThumbnail(
        $productModel,
        $imageModel
    ): void {
        $productPageCBID = SCProduct::productIDToProductPageID(
            CBModel::getCBID(
                $productModel
            )
        );

        $updater = CBModelUpdater::fetchByCBID(
            $productPageCBID
        );

        $productPageSpec = ($updater->CBModelUpdater_getSpec)();

        /* some products don't have pages */

        if ($productPageSpec === null) {
            return;
        }

        $productPageClassName = CBModel::getClassName(
            $productPageSpec
        );

        /* this function only knows how to update a CBViewPage */

        if ($productPageClassName !== 'CBViewPage') {
            return;
        }

        CBViewPage::setThumbnailImage(
            $productPageSpec,
            $imageModel
        );

        ($updater->CBModelUpdater_save)();
    }
    /* updateProductPageThumbnail() */

}
