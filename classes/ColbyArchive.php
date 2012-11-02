<?php

define('COLBY_DATA_DIRECTORY', COLBY_SITE_DIRECTORY . '/data');

class ColbyArchiveAttributes
{
    public $created;
    public $createdBy;
    public $hash;
    public $modified;
    public $modifiedBy;
}

class ColbyArchive
{
    private $archiveId;
    private $lockResource;

    protected $attributes;
    protected $rootObject;

    /**
     * @return void
     */
    public static function delete($archiveId)
    {
        // NOTE: currently doesn't handle nonexistent archive
        //       assumes no subdirectories

        $absoluteArchiveDirectory = COLBY_DATA_DIRECTORY . "/{$archiveId}";

        $files = glob("{$absoluteArchiveDirectory}/*");

        foreach ($files as $file)
        {
            unlink($file);
        }

        rmdir($absoluteArchiveDirectory);
    }

    /**
     * @param string $archiveId
     *
     * @param string $hash The hash of the archive that the caller is currently
     *                     working with. This can be passed as null if you don't
     *                     have a hash or don't care.
     *
     * @return
     *  ColbyArchive instance - if it's a new archive, the hash is null,
     *                          or if the hash matches the hash on disk
     *  false - if hash doesn't match the hash on disk
     */
    public static function open($archiveId, $hash = null)
    {
        if (!preg_match('/^[0-9a-f]{40}$/', $archiveId))
        {
            throw new InvalidArgumentException('archiveId');
        }

        $archive = new ColbyArchive();

        $archive->archiveId = $archiveId;

        $absoluteArchiveDirectory = COLBY_DATA_DIRECTORY . "/{$archiveId}";
        $absoluteArchiveFilename = "{$absoluteArchiveDirectory}/archive.data";

        if (is_dir($absoluteArchiveDirectory))
        {
            $archive->lock(LOCK_SH);

            $data = unserialize(file_get_contents($absoluteArchiveFilename));

            $archive->unlock();

            if ($hash && $data->attributes->hash != $hash)
            {
                return false;
            }

            $archive->attributes = $data->attributes;
            $archive->rootObject = $data->rootObject;

            return $archive;
        }
        else
        {
            // if the caller passed in a hash but the file doesn's exist
            // this is the same as the hash not matching the hash on disk

            if ($hash)
            {
                return false;
            }

            $archive->attributes = new ColbyArchiveAttributes();
            $archive->rootObject = new stdClass();

            return $archive;
        }
    }

    /*
     * @return ColbyArchiveAttributes instance
     */
    public function attributes()
    {
        // TODO: copy?

        return $this->attributes;
    }

    /**
     * @param int $operation Either LOCK_SH or LOCK_EX. Use unlock() function instead of passing LOCK_UN.
     *
     * @return void
     */
    private function lock($operation)
    {
        // TODO: push an error and exception handler here
        //       to unlock and release file resource in case of an error

        // NOTE: flock is a cooperative (advisory) locking mechanism
        //       it only locks out others if they also use flock
        //       which, in this case, is enough
        //       things like file_get_contents or a shell on the webserver
        //       do not participate in flock locks

        // NOTE: this function doesn't protect against multiple locks

        $absoluteLockFilename = COLBY_DATA_DIRECTORY . "/{$this->archiveId}/lock.data";

        $this->lockResource = fopen($absoluteLockFilename, 'w');
        flock($this->lockResource, $operation);
    }

    /**
     * @return the root object
     */
    public function rootObject()
    {
        return $this->rootObject;
    }

    /**
     * @return void
     */
    public function setRootObject($rootObject)
    {
        $this->rootObject = $rootObject;
    }

    /**
     * @return bool
     *  true - if the file was saved successfully
     *  false - if the file has change since we last read it from disk
     */
    public function save()
    {
        $absoluteArchiveDirectory = COLBY_DATA_DIRECTORY . "/{$this->archiveId}";
        $absoluteArchiveFilename = "{$absoluteArchiveDirectory}/archive.data";

        if (!file_exists($absoluteArchiveDirectory))
        {
            mkdir($absoluteArchiveDirectory);
        }

        $this->lock(LOCK_EX);

        // if archive already exists on the disk, make sure it hasn't changed
        // since we read it

        if ($this->attributes->hash)
        {
            $data = unserialize(file_get_contents($absoluteArchiveFilename));

            if ($this->attributes->hash != $data->attributes->hash)
            {
                return false;
            }
        }

        // update file attributes

        $this->attributes->hash = sha1(serialize($this->rootObject));

        $time = time(); // time() same as intval(gmdate('U'));

        if (null === $this->attributes->created)
        {
            $this->attributes->created = $time;
            $this->attributes->createdBy = ColbyUser::currentUserId();
        }

        $this->attributes->modified = $time;
        $this->attributes->modifiedBy = ColbyUser::currentUserId();

        $data = new stdClass();
        $data->attributes = $this->attributes;
        $data->rootObject = $this->rootObject;

        file_put_contents($absoluteArchiveFilename, serialize($data));

        $this->unlock();

        return true;
    }

    /**
     * @return void
     */
    private function unlock()
    {
        flock($this->lockResource, LOCK_UN);
        fclose($this->lockResource);

        $this->lockResource = null;
    }
}
