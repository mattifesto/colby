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
     * This method is not meant to be a full featured delete. Any files in the
     * data store directory must be removed before the method called. This class
     * did not create the files in the data store directory so it does not own
     * them and does not have the authority to delete them.
     *
     * This method does not attempt to remove any intermediate and potentially
     * shared directories that may exist in its path.
     *
     * @return void
     */
    public function delete()
    {
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
        return COLBY_SITE_URL . "/data/{$this->path}";
    }
}
