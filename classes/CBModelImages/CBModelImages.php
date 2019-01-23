<?php

final class CBModelImages {

    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * @param object $args
     *
     *      {
     *          modelID: ID
     *          imageModelID: ID
     *      }
     *
     * @return void
     */
    static function CBAjax_replacePrimaryImage(stdClass $args): void {
        CBModelImages::replacePrimaryImage(
            $args->modelID,
            $args->imageModelID
        );

    }

    /**
     * @return string
     */
    static function CBAjax_replacePrimaryImage_group(): string {
        return 'Administrators';
    }

    /* -- functions -- -- -- -- -- */

    /**
     * @param ID $modelID
     *
     * @return object|null
     */
    static function fetchPrimaryImageModel($modelID): ?stdClass {
        $associationKey = 'CBModelImages_CBModel_CBImage_primary';

        return CBModelAssociations::fetchAssociatedModel(
            $modelID,
            $associationKey
        );
    }

    /**
     * @param ID $modelID
     * @param ID $imageModelID
     *
     * @return void
     */
    static function replacePrimaryImage(
        string $modelID,
        string $imageModelID
    ): void {
        $associationKey = 'CBModelImages_CBModel_CBImage_primary';

        CBModelAssociations::replaceAssociatedID(
            $modelID,
            $associationKey,
            $imageModelID
        );
    }

    /**
     * @TODO 2019_01_23
     *
     *      Uncomment this function when it is first needed.
     *      insertSecondaryImage() or addSecondaryImage() ?
     *      Insert might be good because it suggests SQL activity.
     *
     * @param ID $modelID
     * @param ID $imageModelID
     *
     * @return void
     */ /*
     static function insertSecondaryImage($modelID, $imageModelID): void {
        $associationKey = 'CBModelImages_CBModel_CBImage_secondary';

        CBModelAssociations::add(
            $modelID,
            $associationKey,
            $imageModelID
        );
    } */
}
