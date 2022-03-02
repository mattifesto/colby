<?php

final class
CB_SampleImages
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.60.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        $imageModel1000x5000 = CBModels::fetchModelByCBID(
            CB_SampleImages::getSampleImageModelCBID_1000x5000()
        );

        $imageModel5000x1000 = CBModels::fetchModelByCBID(
            CB_SampleImages::getSampleImageModelCBID_5000x1000()
        );

        return [
            [
                'CB_SampleImages_5000x1000_imageModel_jsvariable',
                $imageModel5000x1000,
            ],
            [
                'CB_SampleImages_1000x5000_imageModel_jsvariable',
                $imageModel1000x5000,
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
        CB_SampleImages::generate5000x1000SampleImage();

        CB_SampleImages::generate1000x5000SampleImage();
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
     * @return void
     */
    static function
    generate1000x5000SampleImage(
    ): void
    {
        $generatedImageDirectory =
        CB_SampleImages::getGeneratedImageDirectory();

        $image = imagecreatetruecolor(
            1000,
            5000
        );

        $red = imagecolorallocate(
            $image,
            255,
            127,
            127
        );

        imagefill(
            $image,
            0,
            0,
            $red
        );

        $white = imagecolorallocate(
            $image,
            255,
            255,
            255,
        );

        imagestring(
            $image,
            5,
            100,
            100,
            'CB_SampleImages PNG 1000 x 5000',
            $white
        );

        $imageFilepath =
        "${generatedImageDirectory}/CB_SampleImages_1000x5000.png";

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
            'CB_SampleImages_1000x5000_association'
        );

        CB_ModelAssociation::setSecondCBID(
            $modelAssociation,
            $imageModelCBID
        );

        CBModelAssociations::delete(
            CB_SampleImages::getModelAssociationFirstCBID(),
            'CB_SampleImages_1000x5000_association'
        );

        CBModelAssociations::insertOrUpdate(
            $modelAssociation
        );
    }
    /* generate1000x5000SampleImage() */



    /**
     * @return void
     */
    static function
    generate5000x1000SampleImage(
    ): void
    {
        $generatedImageDirectory =
        CB_SampleImages::getGeneratedImageDirectory();

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

        $imageFilepath =
        "${generatedImageDirectory}/CB_SampleImages_5000x1000.png";

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
    /* generate5000x1000SampleImage() */



    /**
     * @return string
     */
    static function
    getGeneratedImageDirectory(
    ): string
    {
        $generatedImageDirectory = (
            cb_document_root_directory() .
            '/tmp/generated_images'
        );

        $directoryDoesExist = is_dir(
            $generatedImageDirectory
        );

        if (
            !$directoryDoesExist
        ) {
            mkdir(
                $generatedImageDirectory,
                0777, /* mode */
                true, /* recursive */
            );
        }

        return $generatedImageDirectory;
    }
    /* getGeneratedImageDirectory() */



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
    getSampleImageModelCBID_1000x5000(
    ): string
    {
        $data = CBModelAssociations::fetchOne(
            CB_SampleImages::getModelAssociationFirstCBID(),
            'CB_SampleImages_1000x5000_association'
        );

        return $data->associatedID;
    }
    /* getSampleImageCBID_5000x1000() */



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
