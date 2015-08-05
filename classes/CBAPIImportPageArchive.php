<?php

/**
 * This API uploads a page archive file and then imports it. If a page already
 * exists with the data store ID in the archive that page will be removed
 * before importing the uploaded page.
 */
class CBAPIImportPageArchive extends CBAPI {

    private $dataStore;
    private $pageArchiveFilename;
    private $pageModel;
    private $zipArchive;

    /**
     * @return void
     */
    protected function init() {

        $this->pageArchiveFilename = CBSiteDirectory . '/tmp/' . CBHex160::random() . '.cbpage';

        $this->verifyUploadedFile();

        move_uploaded_file($_FILES['page-archive']['tmp_name'], $this->pageArchiveFilename);

        $this->zipArchive = new ZipArchive();

        if ($this->zipArchive->open($this->pageArchiveFilename) !== true) {

            throw new InvalidArgumentException('The file uploaded is not a page archive.');
        }
    }

    /**
     * @return void
     */
    private function deletePage() {

        $dataStoreID = $this->pageModel->dataStoreID;

        CBPages::deleteRowWithDataStoreID($dataStoreID);
        CBPages::deleteRowWithDataStoreIDFromTheTrash($dataStoreID);

        if (is_dir($this->dataStore->directory())) {

            $this->dataStore->delete();
        }
    }

    /**
     * @return void
     */
    private function importPage() {

        $this->dataStore->makeDirectory();
        $this->zipArchive->extractTo($this->dataStore->directory());

        $page = CBViewPage::initForImportWithID($this->pageModel->dataStoreID);

        $page->save();
    }

    /**
     * @return bool
     */
    protected function process() {

        $pageModelJSON      = $this->zipArchive->getFromName('/model.json');
        $this->pageModel    = json_decode($pageModelJSON);
        $this->dataStore    = new CBDataStore($this->pageModel->dataStoreID);

        $this->deletePage();
        $this->importPage();

        $this->response->message = 'The page was imported successfully.';

        return true;
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

    /**
     * Detects any errors that occurred when the file was uploaded to the
     * server.
     */
    private function verifyUploadedFile()
    {
        if ($_FILES['page-archive']['error'] != UPLOAD_ERR_OK)
        {
            switch ($_FILES['page-archive']['error'])
            {
                case UPLOAD_ERR_INI_SIZE:

                    $maxSize = ini_get('upload_max_filesize');
                    $message = "The file uploaded exceeds the allowed upload size of: {$maxSize}.";

                    break;

                case UPLOAD_ERR_FORM_SIZE:

                    $maxSize = ini_get('post_max_size');
                    $message = "The file uploaded exceeds the allowed post upload size of: {$maxSize}.";

                    break;

                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                default:

                    $message = "File upload error code: {$_FILES[$name]['error']}";
            }

            throw new RuntimeException($message);
        }
    }
}
