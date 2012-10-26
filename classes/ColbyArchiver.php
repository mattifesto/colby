<?php

define('COLBY_DATA_DIRECTORY', COLBY_SITE_DIRECTORY . '/data');

class ColbyArchiver
{
    public static function archiveRootObjectWithFileId($object, $fileId)
    {
        $absoluteRootObjectFilename = COLBY_DATA_DIRECTORY . "/{$fileId}/rootObject.php";

        file_put_contents($absoluteRootObjectFilename, serialize($object));
    }

    public static function createFileWithFileId($fileId)
    {
        $absoluteFileDirectory = COLBY_DATA_DIRECTORY . "/{$fileId}";

        if (file_exists($absoluteFileDirectory))
        {
            throw new RuntimeException('A file with the specified fileId already exists.');
        }

        mkdir($absoluteFileDirectory);
    }

    public static function unarchiveRootObjectWithFileId($fileId)
    {
        $absoluteRootObjectFilename = COLBY_DATA_DIRECTORY . "/{$fileId}/rootObject.php";

        $object = unserialize(file_get_contents($absoluteRootObjectFilename));

        return $object;
    }
}
