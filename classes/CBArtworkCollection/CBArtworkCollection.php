<?php

final class CBArtworkCollection {

    /* -- CBModel interfaces -- */



    /**
     * @param object $artworkCollectionSpec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $artworkCollectionSpec
    ): stdClass {

        /* artworks */

        $potentailArtworkSpecs = CBModel::valueToArray(
            $artworkCollectionSpec,
            'artworks'
        );

        $artworkModels = [];

        foreach ($potentailArtworkSpecs as $potentialArtworkSpec) {
            $artworkSpec = CBConvert::valueAsModel(
                $potentialArtworkSpec,
                [
                    'CBArtwork',
                ]
            );

            if ($artworkSpec !== null) {
                $artworkModel = CBModel::build(
                    $artworkSpec
                );

                array_push(
                    $artworkModels,
                    $artworkModel
                );
            }
        }


        /* done */

        return (object)[
            'artworks' => $artworkModels,
        ];
    }
    /* CBModel_build() */



    /* -- functions -- */



    /**
     * @param object|null $artworkCollectionModel
     *
     * @return object|null
     */
    static function getMainArtwork(
        ?stdClass $artworkCollectionModel
    ): ?stdClass {
        $artworks = CBModel::valueToArray(
            $artworkCollectionModel,
            'artworks'
        );

        if (count($artworks) > 0) {
            return $artworks[0];
        } else {
            return null;
        }
    }
    /* getMainArtwork() */

}
