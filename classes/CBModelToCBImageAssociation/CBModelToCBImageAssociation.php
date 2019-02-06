<?php

final class CBModelToCBImageAssociation {

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
    static function CBAjax_replaceImageID(stdClass $args): void {
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
    static function CBAjax_replaceImageID_group(): string {
        return 'Administrators';
    }
}
