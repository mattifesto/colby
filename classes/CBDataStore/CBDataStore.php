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
final class CBDataStore {

    /**
     * Deletes a data store with "delete if exists" semantics.
     *
     * This method does not attempt to remove any intermediate and potentially
     * shared directories that may exist in its path.
     *
     * @param {hex160} $ID
     *  Because of the "delete if exists" semantics, this function will throw
     *  an exception if the ID is not a hex160 value to avoid situations where
     *  incorrect code believes something has been deleted when it hasn't.
     *
     * @return null
     */
    static function deleteByID($ID) {
        if (!CBHex160::is($ID)) {
            throw new InvalidArgumentException("'{$ID}' is not a valid data store ID.");
        }

        $directory = CBDataStore::directoryForID($ID);

        if (!is_dir($directory)) {
            return;
        }

        $directoryIterator = new RecursiveDirectoryIterator($directory,
            RecursiveDirectoryIterator::SKIP_DOTS);
        $iteratorIterator = new RecursiveIteratorIterator($directoryIterator,
            RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iteratorIterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                unlink($fileInfo->getPathname());
            } else {
                rmdir($fileInfo->getPathname());
            }
        }

        rmdir($directory);
    }

    /**
     * @return string
     */
    static function directoryForID($ID) {
        $directoryName = self::directoryNameFromDocumentRoot($ID);

        return CBSiteDirectory . "/{$directoryName}";
    }

    /**
     * @param string $ID
     *      example: "1ab9879ccb12eaaeda7b81b08fa433fde8bc86e3"
     *
     * @return string
     *      example: "data/1a/b9/879ccb12eaaeda7b81b08fa433fde8bc86e3"
     */
    static function directoryNameFromDocumentRoot($ID) {
        $ID             = strtolower($ID);
        $directoryName  = preg_replace('/^(..)(..)/', '$1/$2/', $ID);

        return "data/{$directoryName}";
    }

    /**
     * @deprecated use CBDataStore::flexpath()
     *
     * Returns an absolute filename given a data store ID and the filename of
     * a file stored inside the data store directory.
     *
     * @param string $args['filename']
     *      This misnamed parameter should be basename (filename.extension).
     * @param string $args['ID']
     *
     * @return string
     */
    static function filepath($args) {
        $filename = $ID = '';
        extract($args, EXTR_IF_EXISTS);

        return CBDataStore::directoryForID($ID) . "/{$filename}";
    }

    /**
     * The parameters of this function are ordered by most likely to be
     * provided. $ID is required, $basename is likely to be provided, and
     * $flexdir is likely but less likely than $basename to be provided.
     *
     * @param hex160 $ID
     * @param string? $basename
     * @param string? $flexdir
     *      This will usually be either CBSiteDirectory or CBSiteURL.
     *
     * @return string
     */
    static function flexpath($ID, $basename = null, $flexdir = null) {
        $flexpath = CBDataStore::directoryNameFromDocumentRoot($ID);

        if (!empty($basename)) {
            $flexpath = "{$flexpath}/{$basename}";
        }

        if (!empty($flexdir)) {
            $flexpath = "{$flexdir}/{$flexpath}";
        }

        return $flexpath;
    }

    /**
     * This function has "create if not exists" semantics.
     *
     * @return null
     */
    static function makeDirectoryForID($ID) {
        $directory = self::directoryForID($ID);

        if (!is_dir($directory)) {
            mkdir($directory, /* mode: */ 0777, /* recursive: */ true);
        }
    }

    /**
     * @deprecated use CBDataStore::flexpath()
     *
     * @param   {hex160}        ID (required)
     * @param   {string}|null   filename
     *
     * @return  {string}
     */
    static function toURL($args) {
        $ID = $filename = null;
        extract($args, EXTR_IF_EXISTS);

        $directoryName  = self::directoryNameFromDocumentRoot($ID);
        $URL            = CBSiteURL . "/{$directoryName}";

        if ($filename) {
            return "{$URL}/{$filename}";
        } else {
            return $URL;
        }
    }
}
