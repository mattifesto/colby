<?php

/**
 * This class is responsible for creating, locating, and deleting Colby data
 * stores.
 *
 * ! This class is a limited functionality core system class. It's meant to be
 * ! simple, obvious, and non-controversial without any non-obvious or tricky
 * ! logic. Any potential additions to the class should be highly scrutinized.
 *
 * 2014.01.29 Don't bother attempting to provide locking functionality to this
 * class or any PHP code unless it is database related. The file locking
 * functionality of PHP isn't very certain and that fact is well documented.
 */
class CBDataStore
{
    private $dataStoreID = null;

    /**
     * @return CBDataStore
     */
    public function __construct($dataStoreID)
    {
        $this->dataStoreID  = $dataStoreID;
        $this->path         = preg_replace('/^(..)(..)/', '$1/$2/', $this->dataStoreID);
    }

    /**
     * This method does not attempt to remove any intermediate and potentially
     * shared directories that may exist in its path.
     *
     * This method behaves similarly to `rmdir` in that it will fail if the
     * data store directory does not exist. If in doubt, call `is_dir` with the
     * value returned by the `directory` function as the parameter.
     *
     * @return void
     */
    public function delete()
    {
        $directoryIterator  = new RecursiveDirectoryIterator($this->directory(),
                                                             RecursiveDirectoryIterator::SKIP_DOTS);
        $iteratorIterator   = new RecursiveIteratorIterator($directoryIterator,
                                                            RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iteratorIterator as $fileInfo)
        {
            if ($fileInfo->isFile())
            {
                unlink($fileInfo->getPathname());
            }
            else
            {
                rmdir($fileInfo->getPathname());
            }
        }

        rmdir($this->directory());
    }

    /**
     * @deprecated use `directoryForID`
     *
     * @return string
     */
    public function directory()
    {
        return CBSiteDirectory . "/data/{$this->path}";
    }

    /**
     * @return string
     */
    public static function directoryForID($ID) {
        $directoryName = self::directoryNameFromDocumentRoot($ID);

        return CBSiteDirectory . "/{$directoryName}";
    }

    /**
     * @return string
     */
    public static function directoryNameFromDocumentRoot($ID) {
        $directoryName = preg_replace('/^(..)(..)/', '$1/$2/', $ID);

        return "data/{$directoryName}";
    }

    /**
     * @deprecated use `makeDirectoryForID`
     *
     * This function has "create if not exists" semantics.
     *
     * @return void
     */
    public function makeDirectory() {
        if (!is_dir($this->directory())) {
            mkdir($this->directory(), /* mode: */ 0777, /* recursive: */ true);
        }
    }

    /**
     * This function has "create if not exists" semantics.
     *
     * @return void
     */
    public static function makeDirectoryForID($ID) {
        $directory = self::directoryForID($ID);

        if (!is_dir($directory)) {
            mkdir($directory, /* mode: */ 0777, /* recursive: */ true);
        }
    }

    /**
     * @return string
     */
    public function URL()
    {
        return CBSiteURL . "/data/{$this->path}";
    }
}
