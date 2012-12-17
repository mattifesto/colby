<?php

define('COLBY_DATA_DIRECTORY', COLBY_SITE_DIRECTORY . '/data');
define('COLBY_DATA_URL', COLBY_SITE_URL . '/data');

class ColbyArchive
{
    private $lockResource;

    protected $attributes;
    protected $data;

    /**
     * @return string
     */
    public function archiveId()
    {
        return $this->data->archiveId;
    }

    /**
     * The Unix timestamp when the archive was first saved.
     *
     *  @return int | null
     */
    public function created()
    {
        return isset($this->attributes->created) ? $this->attributes->created : null;
    }

    /**
     * The user id of the user that first save the archive.
     *
     *  @return int | null
     */
    public function createdBy()
    {
        return isset($this->attributes->createdBy) ? $this->attributes->createdBy : null;
    }

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
     * This function is named "path" because it can return either a directory
     * or a filename depending on the value of the $filename parameter.
     *
     * @return string
     *
     *  The absolute archive directory for this archive or an absolute
     *  filename for a file in the archive directory.
     */
    public function path($filename = null)
    {
        if ($filename)
        {
            return COLBY_DATA_DIRECTORY . "/{$this->data->archiveId}/{$filename}";
        }
        else
        {
            return COLBY_DATA_DIRECTORY . "/{$this->data->archiveId}";
        }
    }

    /**
     * The Unix timestamp when the archive was last modified.
     *
     *  @return int | null
     */
    public function modified()
    {
        return isset($this->attributes->modified) ? $this->attributes->modified : null;
    }

    /**
     * The user id of the user that last saved the archive.
     *
     *  @return int | null
     */
    public function modifiedBy()
    {
        return isset($this->attributes->modifiedBy) ? $this->attributes->modifiedBy : null;
    }

    /**
     * @param string $archiveId
     *
     * @param bool $shouldCreateStorageNow
     *
     *  If this parameter is true the storage area for the archive will be
     *  created before this method returns. This would be desireable if the
     *  caller was preparing to save files into the archive before saving
     *  the archive data and needs to be sure the storage area exists.
     *
     *  If this parameter is false, the default value, then the storage area for
     *  the archive won't be created until the archive is first saved. If the
     *  archive is never saved, the storage area won't be created at all and
     *  opening the archive will have no permanent side effect.
     *
     * @return ColbyArchive | bool
     *
     *  The method returns a ColbyArchive instance if it's a new archive
     *  or if the archive exists on disk. The function returns false
     *  if the hash doesn't match last saved hash.
     */
    public static function open($archiveId, $shouldCreateStorageNow = false)
    {
        if (!preg_match('/^[0-9a-f]{40}$/', $archiveId))
        {
            throw new InvalidArgumentException('archiveId');
        }

        $archive = new ColbyArchive();

        // If an archive file exists, these values will be overwritten.

        $archive->attributes = new stdClass();
        $archive->data = new stdClass();
        $archive->data->archiveId = $archiveId;

        // If an archive exists on the disk, load the data.

        $absoluteArchiveFilename = $archive->path('archive.data');

        if (is_file($absoluteArchiveFilename))
        {
            $archive->lock(LOCK_SH);

            $data = unserialize(file_get_contents($absoluteArchiveFilename));

            $archive->unlock();

            $archive->attributes = $data->attributes;
            $archive->data = $data->data;

            // Since we store the archive id in the file, just do a basic check to make sure it's the same as the archive id requested.

            if ($archive->archiveId() != $archiveId)
            {
                throw new RuntimeException('Data Consistency Error: The archive id stored inside the archive doesn\'t match the external archive id.');
            }
        }
        else if ($shouldCreateStorageNow)
        {
            $absoluteArchiveDirectory = $archive->path();

            if (!is_dir($absoluteArchiveDirectory))
            {
                mkdir($absoluteArchiveDirectory);
            }
        }

        return $archive;
    }

    /**
     * Returns the hash value for the data the last time it was saved. This
     * method does not recompute the hash if the data has changed.
     *
     * @return string
     */
    public function hash()
    {
        return isset($this->attributes->hash) ? $this->attributes->hash : null;
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

        $absoluteLockFilename = $this->path('lock.data');

        $this->lockResource = fopen($absoluteLockFilename, 'w');
        flock($this->lockResource, $operation);
    }

    /**
     * @return stdClass
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @return bool
     *  true - if the file was saved successfully
     *  false - if the file has change since we last read it from disk
     */
    public function save()
    {
        $absoluteArchiveFilename = $this->path('archive.data');

        if (!file_exists($this->path()))
        {
            mkdir($this->path());
        }

        $this->lock(LOCK_EX);

        // if archive already exists on the disk, make sure it hasn't changed
        // since we read it

        if (isset($this->attributes->hash))
        {
            $data = unserialize(file_get_contents($absoluteArchiveFilename));

            if ($this->attributes->hash != $data->attributes->hash)
            {
                return false;
            }
        }

        // update file attributes

        $this->attributes->hash = sha1(serialize($this->data));

        $time = time(); // time() same as intval(gmdate('U'));

        if (!isset($this->attributes->created))
        {
            $this->attributes->created = $time;
            $this->attributes->createdBy = ColbyUser::currentUserId();
        }

        $this->attributes->modified = $time;
        $this->attributes->modifiedBy = ColbyUser::currentUserId();

        $serializableObject = new stdClass();
        $serializableObject->attributes = $this->attributes;
        $serializableObject->data = $this->data;

        file_put_contents($absoluteArchiveFilename, serialize($serializableObject));

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
