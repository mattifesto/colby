<?php

final class SCProductEditor {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          productCBID: CBID
     *      }
     *
     * @return object
     *
     *      {
     *          artworkCollectionSpec: object
     *      }
     */
    static function CBAjax_fetchArtworkCollectionSpec(
        stdClass $args
    ): stdClass {
        $productCBID = CBModel::valueAsCBID(
            $args,
            'productCBID'
        );

        $artworkCollectionCBID = SCProduct::productCBIDToArtworkCollectionCBID(
            $productCBID
        );

        $updater = CBModelUpdater::fetch(
            (object)[
                'className' => 'CBArtworkCollection',
                'ID' => $artworkCollectionCBID,
                'associatedModelCBID' => $productCBID,
            ]
        );

        return (object)[
            'artworkCollectionSpec' => $updater->working,
        ];
    }
    /* CBAjax_fetchArtworkCollectionSpec() */



    /**
     * @return string
     */
    static function CBAjax_fetchArtworkCollectionSpec_getUserGroupClassName(
    ): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v637.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBErrorHandler',
            'CBModel',
            'CBUI',
            'CBUIPanel',
            'CBUISpecEditor',
            'CBUISpecSaver',

            'CBArtworkCollectionEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
