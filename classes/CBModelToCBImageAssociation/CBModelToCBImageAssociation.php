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
        $associationKey =
        'CBModelToCBImageAssociation';

        CBModelAssociations::replaceAssociatedID(
            $args->modelID,
            $associationKey,
            $args->imageID
        );
    }
    /* CBAjax_replaceImageID() */



    /**
     * @return string
     */
    static function CBAjax_replaceImageID_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    // -- functions



    /**
     * @param CBID $modelCBID
     *
     * @return object|null
     */
    static function
    fetchAssociatedImageModelByModelCBID(
        string $modelCBID
    ): ?stdClass
    {
        $associatedImageModelCBID =
        CBModelToCBImageAssociation::fetchAssociatedImageModelCBIDByModelCBID(
            $modelCBID
        );

        if (
            $associatedImageModelCBID === null
        ) {
            return
            null;
        }

        return
        CBModels::fetchModelByCBID(
            $associatedImageModelCBID
        );
    }
    // fetchAssociatedImageModelByModelCBID()



    /**
     * @param CBID $modelCBID
     *
     * @return CBID|null
     */
    static function
    fetchAssociatedImageModelCBIDByModelCBID(
        string $modelCBID
    ): ?string
    {
        $associationKey =
        'CBModelToCBImageAssociation';

        return
        CBModelAssociations::fetchSingularSecondCBID(
            $modelCBID,
            $associationKey
        );
    }
    // fetchAssociatedImageCBIDByModelCBID()

}
