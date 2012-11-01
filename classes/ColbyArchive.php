<?php

define('COLBY_DATA_DIRECTORY', COLBY_SITE_DIRECTORY . '/data');

class ColbyArchiver
{
    /**
     * @return bool | string
     *  false: if the file has been change since it was read
     *         the file will not be written in this case
     *  the updated file hash: if the file is archived successfully
     */
    public static function archiveRootObjectWithFileId($rootObject, $fileId, $previousFileHash)
    {
        // If createFileWithFileId hasn't been called most of the functions
        // below will fail. This enforces that it be called before this function.

        $absoluteRootObjectFilename = COLBY_DATA_DIRECTORY . "/{$fileId}/rootObject.data";

        $lockResource = self::lockFile($fileId);

        try
        {
            $hash = sha1_file($absoluteRootObjectFilename);

            if ($hash != $previousFileHash)
            {
                $result = false;

                goto done;
            }

            // TODO: update file metadata on the root object before saving

            // $rootObject->fileAttributes = ...;

            $serializedRootObject = serialize($rootObject);

            file_put_contents($absoluteRootObjectFilename, $serializedRootObject);

            $result = sha1($serializedRootObject);
        }
        catch (Exception $exception)
        {
            self::unlock($lockResource);

            throw $exception;
        }

        done:

        self::unlock($lockResource);

        return $result;
    }

    /**
     * @return string
     *  the new file hash
     */
    public static function createFileWithRootObjectAndFileId($rootObject, $fileId)
    {
        if (!preg_match('/^[0-9a-f]{40}$/', $fileId))
        {
            throw new InvalidArgumentException('fileId');
        }

        $absoluteFileDirectory = COLBY_DATA_DIRECTORY . "/{$fileId}";

        if (file_exists($absoluteFileDirectory))
        {
            throw new RuntimeException('A file with the specified fileId already exists.');
        }

        mkdir($absoluteFileDirectory);

        $absoluteRootObjectFilename = COLBY_DATA_DIRECTORY . "/{$fileId}/rootObject.data";

        $lockResource = self::lockFile($fileId);

        try
        {
            $serializedRootObject = serialize($rootObject);

            file_put_contents($absoluteRootObjectFilename, $serializedRootObject);

            $result = sha1($serializedRootObject);
        }
        catch (Exception $exception)
        {
            self::unlock($lockResource);

            throw $exception;
        }

        self::unlock($lockResource);

        return $result;
    }

    /**
     * @return file handle
     *  the file handle resource that was used to take the lock
     *  this is not useful except that it needs to be passed to unlock
     */
    private static function lockFile($fileId)
    {
        // NOTE: flock is a cooperative (advisory) locking mechanism
        //       it only locks out others if they also use flock
        //       which, in this case, is enough
        //       things like file_get_contents or a shell on the webserver
        //       do not participate in flock locks

        $absoluteLockFilename = COLBY_DATA_DIRECTORY . "/{$fileId}/lock.data";

        $lockResource = fopen($absoluteLockFilename, 'w');
        flock($lockResource, LOCK_EX);

        return $lockResource;
    }

    /**
     * @return object
     *  object->fileHash: the file hash
     *  object->rootObject: the root object
     */
    public static function unarchiveRootObjectWithFileId($fileId)
    {
        $absoluteRootObjectFilename = COLBY_DATA_DIRECTORY . "/{$fileId}/rootObject.data";

        $lockResource = self::lockFile($fileId);

        try
        {
            $result = new stdClass();

            $serializedRootObject = file_get_contents($absoluteRootObjectFilename);

            $result->fileHash = sha1($serializedRootObject);
            $result->rootObject = unserialize($serializedRootObject);
        }
        catch (Exception $exception)
        {
            self::unlock($lockResource);

            throw $exception;
        }

        self::unlock($lockResource);

        return $result;
    }

    /**
     * @return void
     */
    private static function unlock($lockResource)
    {
        flock($lockResource, LOCK_UN);
        fclose($lockResource);
    }
}
