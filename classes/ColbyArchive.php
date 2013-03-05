<?php

define('COLBY_DATA_DIRECTORY', COLBY_SITE_DIRECTORY . '/data');
define('COLBY_DATA_URL', COLBY_SITE_URL . '/data');

class ColbyArchive
{
    private $lockResource;

    protected $attributes;
    protected $data;

    /**
     * @return ColbyArchive
     */
    public static function archiveFromGetData()
    {
        $archiveId = isset($_GET['archive-id']) ? $_GET['archive-id'] : '';

        if (empty($archiveId))
        {
            $archiveId = sha1(microtime() . rand());

            $parts = explode('?', $_SERVER['REQUEST_URI']);

            $path = $parts[0];

            $queryString = isset($parts[1]) ? $parts[1] : '';
            $queryString = "archive-id={$archiveId}&{$queryString}";

            header("Location: {$path}?{$queryString}");
            ob_end_clean();
            exit;
        }

        $archive = ColbyArchive::open($archiveId);
        $archive->model = ColbyPageModel::modelWithArchive($archive);

        if (!$archive->model->viewId())
        {
            $archive->model->setViewId($_GET['view-id']);
        }

        return $archive;
    }

    /**
     * @return ColbyArchive
     */
    public static function archiveFromPostData()
    {
        $archive = ColbyArchive::open($_POST['archive-id']);
        $archive->model = ColbyPageModel::modelWithArchive($archive);

        if (!$archive->model->viewId())
        {
            $archive->model->setViewId($_POST['view-id']);
        }

        $archive->setStringValueForKey($_POST['title'], 'title');
        $archive->setStringValueForKey($_POST['subtitle'], 'subtitle');
        $archive->setBoolValueForKey($_POST['stub-is-locked'], 'stubIsLocked');
        $archive->setStringValueForKey($_POST['custom-page-stub-text'], 'customPageStubText');

        $archive->model->setPreferredPageStub($_POST['preferred-page-stub']);

        $archive->model->setPublicationData($_POST['is-published'],
                                            $_POST['published-by'],
                                            $_POST['publication-date']);

        return $archive;
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
     * Creates the directory where the archive data will be saved if it the
     * directory doesn't exist yet.
     *
     * @return void
     */
    private function createStorage()
    {
        $dataPath = self::dataPathForArchiveId($this->data->archiveId);

        $directories = explode('/', $dataPath);

        $absoluteDirectory = COLBY_DATA_DIRECTORY;

        foreach($directories as $directory)
        {
            $absoluteDirectory = "{$absoluteDirectory}/$directory";

            if (!is_dir($absoluteDirectory))
            {
                mkdir($absoluteDirectory);
            }
        }
    }

    /**
     * @return stdClass
     */
    public function data()
    {
        return $this->data;
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
    public static function dataPathForArchiveId($archiveId)
    {
        return preg_replace('/^(..)(..)/', '$1/$2/', $archiveId);
    }

    /**
     * @return void
     */
    public static function delete($archiveId)
    {
        $sqlArchiveId = Colby::mysqli()->escape_string($archiveId);

        $sql = "DELETE FROM `ColbyPages` WHERE `archiveId` = UNHEX('{$sqlArchiveId}')";

        Colby::query($sql);

        // NOTE: currently doesn't handle nonexistent archive
        //       assumes no subdirectories

        $dataPath = self::dataPathForArchiveId($archiveId);

        $absoluteArchiveDirectory = COLBY_DATA_DIRECTORY . "/{$dataPath}";

        $files = glob("{$absoluteArchiveDirectory}/*");

        foreach ($files as $file)
        {
            unlink($file);
        }

        rmdir($absoluteArchiveDirectory);
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
     * @return bool
     *  Returns true if the archive exists on disk, otherwise false.
     */
    public static function exists($archiveId)
    {
        $dataPath = self::dataPathForArchiveId($archiveId);

        $absoluteArchiveFilename = COLBY_DATA_DIRECTORY . "/{$dataPath}/archive.data";

        return is_file($absoluteArchiveFilename);
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

        $absoluteArchiveFilename = $archive->path('archive.data');

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
                 && !is_dir($archive->path()))
        {
            $archive->createStorage();
        }

        $archive->model = ColbyPageModel::modelWithArchive($archive);

        return $archive;
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
        $dataPath = self::dataPathForArchiveId($this->data->archiveId);

        if ($filename)
        {
            return COLBY_DATA_DIRECTORY . "/{$dataPath}/{$filename}";
        }
        else
        {
            return COLBY_DATA_DIRECTORY . "/{$dataPath}";
        }
    }

    /**
     * @return bool
     *  true - if the file was saved successfully
     *  false - if the file has change since we last read it from disk
     */
    public function save()
    {
        $this->model->updateDatabase();

        $absoluteArchiveFilename = $this->path('archive.data');

        if (!is_dir($this->path()))
        {
            $this->createStorage();
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
     * @param bool $boolValue
     *
     * @param string $key
     *
     * @return void
     */
    public function setBoolValueForKey($boolValue, $key)
    {
        $key = strval($key);

        $this->data->$key = !!$boolValue;
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

        $this->data->$key = floatval($floatValue);
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

        $this->data->$key = intval($intValue);
    }

    /**
     * This method sets a markaround value for the given name. It also converts
     * the markaround to HTML and sets the HTML value for the key "{$key}HTML".
     *
     * @param string $markaroundValue
     *
     * The value of this parameter is saved as the value for $key. It is then
     * converted to HTML and saved as the value for the key "{$key}HTML".
     *
     * @param string $key
     *
     * @return void
     */
    public function setMarkaroundValueForKey($markaroundValue, $key)
    {
        $key = strval($key);

        $this->data->$key = strval($markaroundValue);

        $htmlKey = "{$key}HTML";

        $this->data->$htmlKey = ColbyConvert::markaroundToHTML($this->data->$key);
    }

    /**
     * Deprecated
     *  2013.01.14
     *
     * @return void
     */
    public function setMarkdownValueForKey($markdownValue, $key)
    {
        Colby::debugLog('`ColbyArchive::setMarkdownValueForKey` has been deprecated in favor of `ColbyArchive::setMarkaroundValueForKey`');

        self::setMarkaroundValueForKey($markdownValue, $key);
    }

    /**
     * This method sets a string value for the given name.
     *
     * @param string $stringValue
     *
     * @param string $key
     *
     * @param bool $shouldAlsoSetHTMLStringValue
     *
     *  This parameter specifies whether in addition to setting the value for
     *  $key to $stringValue, an HTML escaped version of the string should be
     *  generated and set as the value for the key "{$key}HTML".
     *
     * @return void
     */
    public function setStringValueForKey($stringValue, $key, $shouldAlsoSetHTMLStringValue = true)
    {
        $key = strval($key);

        $this->data->$key = strval($stringValue);

        if ($shouldAlsoSetHTMLStringValue)
        {
            $htmlKey = "{$key}HTML";

            $this->data->$htmlKey = ColbyConvert::textToHTML($this->data->$key);
        }
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
     * @return string
     *
     *  The absolute archive URL for this archive or an absolute URL for a file
     *  in the archive directory.
     */
    public function url($filename = null)
    {
        $dataPath = self::dataPathForArchiveId($this->data->archiveId);

        if ($filename)
        {
            return COLBY_DATA_URL . "/{$dataPath}/{$filename}";
        }
        else
        {
            return COLBY_DATA_URL . "/{$dataPath}";
        }
    }

    /**
     * @return mixed
     *
     *  This method returns the value that was set for the key.
     */
    public function valueForKey($key)
    {
        return isset($this->data->$key) ? $this->data->$key : null;
    }
}
