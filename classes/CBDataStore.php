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
     * @return string
     */
    public function directory()
    {
        return CBSiteDirectory . "/data/{$this->path}";
    }

    /**
     * @return string
     */
    public function directoryNameFromDocumentRoot() {

        return "data/{$this->path}";
    }

    /**
     * This class expects that the creation of a data store directory is an
     * important event and it happens exactly when it needs to happen and at
     * no other time. Clients need to plan for when in the lifetime of the
     * data stores they use that the directory is created and call this method
     * precisely at that time.
     *
     * @return void
     */
    public function makeDirectory()
    {
        mkdir($this->directory(), /* mode: */ 0777, /* recursive: */ true);
    }

    /**
     * @return string
     */
    public function URL()
    {
        return CBSiteURL . "/data/{$this->path}";
    }
}
