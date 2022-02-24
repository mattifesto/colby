<?php

/**
 * This model contains an array of CBArtwork models. A CBArtworkCollection model
 * may be saved on its own but associated with another model in cases where that
 * model is imported and can't be edited.
 *
 * In that scenario, the associated model should generate the
 * CBArtworkCollection's CBID based on its own CBID or use the associations
 * table.
 */
final class
CBArtworkCollection
{
    /* -- CBModel interfaces -- */



    /**
     * @param object $artworkCollectionSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $artworkCollectionSpec
    ): stdClass
    {
        /* artworks */

        $potentailArtworkSpecs = CBArtworkCollection::getArtworks(
            $artworkCollectionSpec
        );

        $artworkModels = [];

        foreach (
            $potentailArtworkSpecs as $potentialArtworkSpec
        ) {
            $artworkSpec = CBConvert::valueAsModel(
                $potentialArtworkSpec,
                [
                    'CBArtwork',
                ]
            );

            if (
                $artworkSpec !== null
            ) {
                $artworkModel = CBModel::build(
                    $artworkSpec
                );

                $thumbnailImageURL = CBArtwork::getThumbnailImageURL(
                    $artworkModel
                );

                if (
                    $thumbnailImageURL !== ''
                ) {
                    array_push(
                        $artworkModels,
                        $artworkModel
                    );
                }
            }
        }


        /* associated model CBID */

        $associatedModelCBID = CBModel::valueAsCBID(
            $artworkCollectionSpec,
            'associatedModelCBID'
        );


        /* done */

        return (object)[
            'artworks' => $artworkModels,
            'associatedModelCBID' => $associatedModelCBID,
        ];
    }
    /* CBModel_build() */



    /* -- CBModels interfaces -- */



    /**
     * @param [CBID] $artworkCollectionModelCBIDs
     *
     * @return void
     */
    static function
    CBModels_willDelete(
        array $artworkCollectionModelCBIDs
    ): void
    {
        $artworkCollectionModels = CBModels::fetchModelsByID2(
            $artworkCollectionModelCBIDs
        );

        $associatedModelCBIDs = array_map(
            function (
                $artworkCollectionModel
            ) {
                return CBModel::valueAsCBID(
                    $artworkCollectionModel,
                    'associatedModelCBID'
                );
            },
            $artworkCollectionModels
        );

        $associatedModelCBIDs = array_filter(
            $associatedModelCBIDs
        );

        CBTasks2::restart(
            'CBArtworkCollection_UpdatedTask',
            $associatedModelCBIDs
        );
    }
    /* CBModels_willDelete() */



    /**
     * @param [object] $artworkCollectionModels
     *
     * @return void
     */
    static function
    CBModels_willSave(
        array $artworkCollectionModels
    ): void {
        $associatedModelCBIDs = array_map(
            function (
                $artworkCollectionModel
            ) {
                return CBModel::valueAsCBID(
                    $artworkCollectionModel,
                    'associatedModelCBID'
                );
            },
            $artworkCollectionModels
        );

        $associatedModelCBIDs = array_filter(
            $associatedModelCBIDs
        );

        CBTasks2::restart(
            'CBArtworkCollection_UpdatedTask',
            $associatedModelCBIDs
        );
    }
    /* CBModels_willSave() */



    /* -- accessors -- */



    /**
     * @param object $artworkCollectionModel
     *
     * @return [object]
     */
    static function
    getArtworks(
        $artworkCollectionModel
    ): array
    {
        return CBModel::valueToArray(
            $artworkCollectionModel,
            'artworks'
        );
    }
    /* getArtworks() */



    /* -- functions -- */



    /**
     * @param object|null $artworkCollectionModel
     *
     * @return object|null
     */
    static function
    getMainArtwork(
        ?stdClass $artworkCollectionModel
    ): ?stdClass
    {
        $artworks = CBModel::valueToArray(
            $artworkCollectionModel,
            'artworks'
        );

        if (
            count($artworks) > 0
        ) {
            return $artworks[0];
        } else {
            return null;
        }
    }
    /* getMainArtwork() */

}
