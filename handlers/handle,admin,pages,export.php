<?php

class CBPageExporter {

    private $dataStore;
    private $dataStoreID;
    private $destinationFilename;
    private $zipArchive;
    private $zipArchiveFilename;

    /**
     * @return void
     */
    private function addFilesToArchive() {

        $directoryIterator  = new RecursiveDirectoryIterator($this->dataStore->directory(),
                                                             RecursiveDirectoryIterator::SKIP_DOTS);
        $iteratorIterator   = new RecursiveIteratorIterator($directoryIterator,
                                                            RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iteratorIterator as $fileInfo)
        {
            if ($fileInfo->isFile())
            {
                $absolute = $fileInfo->getPathname();
                $relative = str_replace($this->dataStore->directory(), '', $absolute);

                $this->zipArchive->addFile($absolute, $relative);
            }
            else
            {
                /**
                 * Directories seem to be automatically created so no code is
                 * required here. If this turns out to be false add code here
                 * to add an empty directory.
                 */
            }
        }
    }

    /**
     * @return void
     */
    private function init() {

        if (!isset($_GET['data-store-id'])) {

            throw new InvalidArgumentException('data-store-id');
        }

        $this->dataStoreID = $_GET['data-store-id'];

        if (!preg_match('/^[a-f0-9]{40}$/', $this->dataStoreID)) {

            throw new InvalidArgumentException('data-store-id');
        }
    }

    /**
     * @return void
     */
    public function main() {

        if ($this->userIsAuthorized()) {

            $this->init();
            $this->process();
            $this->send();

        } else {

            include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }
    }

    /**
     * @return void
     */
    private function process() {

        $this->dataStore            = new CBDataSTore($this->dataStoreID);
        $this->destinationFilename  = "{$this->dataStoreID}.cbpage";
        $this->zipFilename          = CBSiteDirectory . "/tmp/{$this->dataStoreID}.cbpage";
        $this->zipArchive           = new ZipArchive();

        if ($this->zipArchive->open($this->zipFilename, ZipArchive::CREATE) !== true) {

            throw new RuntimeException('There was a problem creating the page archive.');
        }

        $this->addFilesToArchive();

        if (!$this->zipArchive->close()) {

            throw new RuntimeException('There was a problem closing the page archive.');
        }
    }

    /**
     * @return void
     */
    private function send() {

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"{$this->dataStoreID}.cbpage\"; filename*=UTF-8''{$this->destinationFilename}");
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($this->zipFilename));

        readfile($this->zipFilename);
        unlink($this->zipFilename);
    }

    /**
     * @return bool
     */
    private function userIsAuthorized() {

        return ColbyUser::current()->isOneOfThe('Administrators');
    }

}

$exporter = new CBPageExporter();

$exporter->main();
