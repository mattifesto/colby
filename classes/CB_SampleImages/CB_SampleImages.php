<?php

final class
CB_SampleImages
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        $imageModel5000x1000 = CBModels::fetchModelByCBID(
            CB_SampleImages::getSampleImageModelCBID_5000x1000()
        );

        return [
            [
                'CB_SampleImages_5000x1000_imageModel_jsvariable',
                $imageModel5000x1000,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */




    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        $directoryPath = (
            cb_document_root_directory() .
            '/tmp/generated_images'
        );

        $directoryDoesExist = is_dir(
            $directoryPath
        );

        if (
            !$directoryDoesExist
        ) {
            mkdir(
                $directoryPath,
                0777, /* mode */
                true, /* recursive */
            );
        }

        $image = imagecreatetruecolor(
            5000,
            1000
        );

        $uwPurple = imagecolorallocate(
            $image,
            51,
            0,
            111
        );

        imagefill(
            $image,
            0,
            0,
            $uwPurple
        );

        $uwGold = imagecolorallocate(
            $image,
            232,
            211,
            162,
        );

        imagestring(
            $image,
            5,
            100,
            100,
            'CB_SampleImages PNG 5000 x 1000',
            $uwGold
        );

        $imageFilepath = "${directoryPath}/CB_SampleImages_5000x1000.png";

        imagepng(
            $image,
            $imageFilepath
        );

        $imageModel = CBImages::URIToCBImage(
            $imageFilepath
        );

        $imageModelCBID = CBModel::getCBID(
            $imageModel
        );

        $modelAssociation = CBModel::createSpec(
            'CB_ModelAssociation'
        );

        CB_ModelAssociation::setFirstCBID(
            $modelAssociation,
            CB_SampleImages::getModelAssociationFirstCBID()
        );

        CB_ModelAssociation::setAssociationKey(
            $modelAssociation,
            'CB_SampleImages_5000x1000_association'
        );

        CB_ModelAssociation::setSecondCBID(
            $modelAssociation,
            $imageModelCBID
        );

        CBModelAssociations::delete(
            CB_SampleImages::getModelAssociationFirstCBID(),
            'CB_SampleImages_5000x1000_association'
        );

        CBModelAssociations::insertOrUpdate(
            $modelAssociation
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array
    {
        return [
            'CB_ModelAssociation',
            'CBImages',
            'CBModel',
            'CBModelAssociations',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- */



    /**
     * @return CBID
     *
     *      Returns the CBID that is used as the first CBID in model
     *      associations made by this class to provide access to the sample
     *      images.
     */
    static function
    getModelAssociationFirstCBID(
    ): string
    {
        return '179949916203a795cf630923b5a5dc80aa216956';
    }
    /* getModelAssociationFirstCBID() */



    /**
     * @return CBID
     *
     *      Returns the image model CBID of the sample image that is 5000 x 1000
     *      pixels.
     */
    static function
    getSampleImageModelCBID_5000x1000(
    ): string
    {
        $data = CBModelAssociations::fetchOne(
            CB_SampleImages::getModelAssociationFirstCBID(),
            'CB_SampleImages_5000x1000_association'
        );

        return $data->associatedID;
    }
    /* getSampleImageCBID_5000x1000() */

}
