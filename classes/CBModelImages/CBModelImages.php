<?php

final class CBModelImages {

    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * @param object $args
     *
     *      {
     *          modelID: ID
     *          imageID: ID
     *      }
     *
     * @return void
     */
    static function CBAjax_replaceImagesModel(stdClass $args): void {
        $associationKey = __CLASS__;

        CBModelAssociations::replaceAssociatedID(
            $args->modelID,
            $associationKey,
            $args->imageID
        );
    }

    /**
     * @return string
     */
    static function CBAjax_replaceImagesModel_group(): string {
        return 'Administrators';
    }
}
