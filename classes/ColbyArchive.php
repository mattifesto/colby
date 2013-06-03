<?php

define('COLBY_DATA_DIRECTORY', COLBY_SITE_DIRECTORY . '/data');
define('COLBY_DATA_URL', COLBY_SITE_URL . '/data');

class ColbyArchive
{
    /**
     * The `$attributes` property will be set to the object holding the
     * attributes read from and saved to the archive data file.
     */
    protected $attributes = null;

    /**
     * The `$data` property will be set to the object holding the archive data
     * read from and saved to the archive data file.
     */
    protected $data = null;

    /**
     * The `$lockResource` will be set to the file resource that is locked
     * when this class needs exclusive access to archive data.
     */
    private $lockResource = null;

    /**
     * Code that updates the document will set the `$searchText` property to
     * the search text to be written to the `ColbyDocuments` table when the
     * archive is saved.
     */
    public $searchText = '';

    /**
     * The `$documentRowId` property will be set to the`id` column value of this
     * archive's `ColbyDocuments` table row the first time it is queried so that
     * future database queries can use it. However, it's easiest to call the
     * `documentRowId` method which will also retrieve it from the database
     * if necessary.
     */
    private $documentRowId = null;


    /**
     * An archive instance should only be created using the `open` method.
     */
    private function __construct()
    {
    }

    /**
     * @return string
     *  The absolute data directory for this archive.
     */
    public function absoluteDataDirectory()
    {
        return self::absoluteDataDirectoryForArchiveId($this->data->archiveId);
    }

    /**
     * @return string
     *  The absolute data directory for the archive with the id `archiveId`.
     */
    public static function absoluteDataDirectoryForArchiveId($archiveId)
    {
        return COLBY_DATA_DIRECTORY . '/' . self::relativeDataPathForArchiveId($archiveId);
    }

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
     * @return string
     *  The data URL for this archive.
     */
    public function dataURL()
    {
        return self::dataURLForArchiveId($this->data->archiveId);
    }

    /**
     * @return string
     *  The data URL for the archive with the id `archiveId`.
     */
    public function dataURLForArchiveId($archiveId)
    {
        return COLBY_DATA_URL . '/' . self::relativeDataPathForArchiveId($archiveId);
    }

    /**
     * Deletes an archive from disk.
     *
     * @return void
     */
    public static function deleteArchiveWithArchiveId($archiveId)
    {
        $archiveDirectory = ColbyArchive::absoluteDataDirectoryForArchiveId($archiveId);

        if (!is_dir($archiveDirectory))
        {
            return;
        }

        $files = glob("{$archiveDirectory}/*");

        foreach ($files as $file)
        {
            unlink($file);
        }

        /**
         * Remove the archive directory and any empty directories below it
         * until we reach the data directory.
         */

        $directory = $archiveDirectory;

        while (COLBY_DATA_DIRECTORY != $directory)
        {
            rmdir($directory);

            /**
             * Go back one directory: '/foo/bar/baz' --> '/foo/bar'
             */

            $directory = preg_replace('/\/[^\/]+$/', '', $directory);

            /**
             * If we reach a non-empty directory then stop.
             */

            if (glob("{$directory}/*"))
            {
                break;
            }
        }
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

        $absoluteLockFilename = $this->absoluteDataDirectory() . '/lock.data';

        $this->lockResource = fopen($absoluteLockFilename, 'w');
        flock($this->lockResource, $operation);
    }

    /**
     * The Unix timestamp when the archive was last saved.
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
     * @return ColbyArchive
     *
     *  The method returns a ColbyArchive instance.
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

        $absoluteArchiveFilename = $archive->absoluteDataDirectory() . '/archive.data';

        if (is_file($absoluteArchiveFilename))
        {
            $archive->lock(LOCK_SH);

            $data = unserialize(file_get_contents($absoluteArchiveFilename));

            $archive->unlock();

            // In most cases the "attributes" and "data" properties will be set. In the case of a malformed archive they will not be set. We aren't exactly interested in making these cases work, but we are interested in not crashing when they occur. For instance, the built-in archive viewer should still be able to open the archive without error to see if there is a title available. Saving a malformed archive after opening it will lose any of the malformed data, which is probably the best behavior we could hope for. It's a better option than making the archive inaccessible.

            if (isset($data->attributes))
            {
                $archive->attributes = $data->attributes;
            }

            if (isset($data->data))
            {
                $archive->data = $data->data;

                // This code once checked to see if the archiveId inside the archive matched the archiveId used to open the archive. At first, if the two didn't match it would throw an exception. However, the only realistic scenario where this would happen would be a mistake, so then the code was changed to set the inner achiveId in this case. But there's no need to check if the archiveIds matched, instead the inner archiveId could just always be set. If it was the same, it would stay the same. If it was unset or different it would be corrected. Less code, same result. This explanation is needed to understand why the code does something often redundant and kind of weird here.

                $archive->data->archiveId = $archiveId;
            }
        }
        else if (   $shouldCreateStorageNow
                 && !is_dir($archive->absoluteDataDirectory()))
        {
            mkdir($archive->absoluteDataDirectory(), 0777, true);
        }

        return $archive;
    }

    /**
     * How this function works:
     *
     * $archiveId parameter value:
     *  '4cbd040533a2f43fc6691d773d510cda70f4126a'
     *
     * return value:
     *  '4c/bd/040533a2f43fc6691d773d510cda70f4126a'
     *
     * The function replaces the first four characters: '4cbd' with '4c/bd/'
     * but returns the whole string.
     *
     * @return string
     *  Returns a relative path to the archive's data inside the data directory.
     */
    public static function relativeDataPathForArchiveId($archiveId)
    {
        return preg_replace('/^(..)(..)/', '$1/$2/', $archiveId);
    }

    /**
     * @return bool
     *  true - if the file was saved successfully
     *  false - if the file has change since we last read it from disk
     */
    public function save()
    {
        $absoluteArchiveFilename = $this->absoluteDataDirectory() . '/archive.data';

        if (!is_dir($this->absoluteDataDirectory()))
        {
            mkdir($this->absoluteDataDirectory(), 0777, true);
        }

        $this->lock(LOCK_EX);

        // if archive already exists on the disk, make sure it hasn't changed
        // since we read it
        // TODO:
        // This code is wrong. The place for this check is not in the middle of
        // the `save` method. Only if the caller cares the caller should check
        // to see if the hashes differ. Otherwise a call to `save` always
        // saves.

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
     * @param bool $boolValue
     *
     * @param string $key
     *
     * @return void
     */
    public function setBoolValueForKey($boolValue, $key)
    {
        $key = strval($key);

        if ($boolValue === null || $boolValue === '')
        {
            $boolValue = null;
        }
        else
        {
            $boolValue = !!$boolValue;
        }

        $this->data->$key = $boolValue;
    }

    /**
     * @param float $floatValue
     *
     * @param string $key
     *
     * @return void
     */
    public function setFloatValueForKey($floatValue, $key)
    {
        $key = strval($key);

        if ($floatValue === null || $floatValue === '')
        {
            $floatValue = null;
        }
        else
        {
            $floatValue = floatval($floatValue);
        }

        $this->data->$key = $floatValue;
    }

    /**
     * @param int $intValue
     *
     * @param string $key
     *
     * @return void
     */
    public function setIntValueForKey($intValue, $key)
    {
        $key = strval($key);

        if ($intValue === null || $intValue === '')
        {
            $intValue = null;
        }
        else
        {
            $intValue = intval($intValue);
        }

        $this->data->$key = $intValue;
    }

    /**
     * This method sets a string value for the given name.
     *
     * @param string $stringValue
     *
     * @param string $key
     *
     * @return void
     */
    public function setStringValueForKey($stringValue, $key)
    {
        $key = strval($key);

        $this->data->$key = strval($stringValue);
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

    /**
     * @return void
     */
    public function unsetValueForKey($key)
    {
        $key = strval($key);

        unset($this->data->$key);
    }

    /**
     * @return mixed
     *
     *  This method returns the value that was set for the key.
     */
    public function valueForKey($key)
    {
        $key = strval($key);

        return isset($this->data->$key) ? $this->data->$key : null;
    }
}
