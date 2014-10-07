<?php

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

        $response               = $this->response;
        $response->actualWidth  = $uploader->sizeX();
        $response->actualHeight = $uploader->sizeY();
        $response->filename     = $filename;
        $response->URL          = $dataStore->URL() . "/{$filename}";
        $response->URLForHtml   = ColbyConvert::textToHTML($response->URL);
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
