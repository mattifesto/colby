<?php

/**
 * This API uploads an image to a data store and optionally creates a resized
 * version of the image.
 *
 * The resizing operation first reduces the image size and then applies the
 * cropping. This is because the dimension of the original image may not be
 * known to the caller. Since the caller specifies the image size parameters
 * they will be able to product appropriate cropping parameters.
 */
class CBAPIUploadImage extends CBAPI {

    private $cropToHeight   = null;
    private $cropToWidth    = null;
    private $dataStoreID    = null;
    private $reduceToHeight = null;
    private $reduceToWidth  = null;
    private $sizeIdentifier = '';

    /**
     * @return void
     */
    protected function init() {

        $this->dataStoreID          = $_POST['dataStoreID'];

        if (isset($_POST['reduceToWidth'])) {

            $this->reduceToWidth    = (int)$_POST['reduceToWidth'];
            $this->sizeIdentifier  .= "rx{$this->reduceToWidth}";
        }

        if (isset($_POST['reduceToHeight'])) {

            $this->reduceToHeight   = (int)$_POST['reduceToHeight'];
            $this->sizeIdentifier  .= "ry{$this->reduceToHeight}";
        }

        if (isset($_POST['cropToWidth'])) {

            $this->cropToWidth      = (int)$_POST['cropToWidth'];
            $this->sizeIdentifier  .= "cx{$this->cropToWidth}";
        }

        if (isset($_POST['cropToHeight'])) {

            $this->cropToHeight     = (int)$_POST['cropToHeight'];
            $this->sizeIdentifier  .= "cy{$this->cropToHeight}";
        }
    }

    /**
     * @return bool
     */
    protected function createResizedImage() {

    }

    /**
     * @return bool
     */
    protected function process() {

        $this->processUploadedImage();

        if ($this->sizeIdentifier)
        {
            $this->createResizedImage();
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function processUploadedImage() {

        $dataStore          = new CBDataStore($this->dataStoreID);
        $uploader           = ColbyImageUploader::uploaderForName('image');
        $filename           = $uploader->sha1() . $uploader->canonicalExtension();
        $absoluteFilename   = $dataStore->directory() . "/{$filename}";

        $uploader->moveToFilename($absoluteFilename);

        if ($this->sizeIdentifier) {

            $destinationFilename    = $dataStore->directory() .
                                      '/' .
                                      $uploader->sha1() .
                                      "-{$this->sizeIdentifier}" .
                                      $uploader->canonicalExtension();

            $this->resizeImage($absoluteFilename, $destinationFilename);

        } else {

            $response               = $this->response;
            $response->actualWidth  = $uploader->sizeX();
            $response->actualHeight = $uploader->sizeY();
            $response->filename     = $filename;
            $response->URL          = $dataStore->URL() . "/{$filename}";
            $response->URLForHTML   = ColbyConvert::textToHTML($response->URL);
        }
    }

    /**
     * @return void
     */
    protected function resizeImage($sourceFilename, $destinationFilename) {

        $dataStore  = new CBDataStore($this->dataStoreID);
        $resizer    = ColbyImageResizer::resizerForFilename($sourceFilename);

        if ($this->reduceToWidth) {

            $resizer->reduceWidthTo($this->reduceToWidth);
        }

        if ($this->reduceToHeight) {

            $resizer->reduceHeightTo($this->reduceToHeight);
        }

        if ($this->cropToWidth) {

            $resizer->cropFromCenterToWidth($this->cropToWidth);
        }

        if ($this->cropToHeight) {

            $resizer->cropFromCenterToHeight($this->cropToHeight);
        }

        $resizer->saveToFilename($destinationFilename);

        $size                   = getimagesize($destinationFilename);
        $response               = $this->response;
        $response->actualWidth  = $size[0];
        $response->actualHeight = $size[1];
        $response->filename     = basename($destinationFilename);
        $response->URL          = $dataStore->URL() . "/{$response->filename}";
        $response->URLForHTML   = ColbyConvert::textToHTML($response->URL);
    }

    /**
     * @return bool
     */
    protected function userIsAuthorized() {

        if (ColbyUser::current()->isOneOfThe('Administrators')) {

            return true;

        } else {

            return parent::userIsAuthorized();
        }
    }
}
