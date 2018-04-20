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
     * This function has "create if not exists" semantics.
     *
     * @return null
     */
    static function create($ID) {
        $directory = CBDataStore::directoryForID($ID);

        if (!is_dir($directory)) {
            mkdir($directory, /* mode: */ 0777, /* recursive: */ true);
        }

        CBDataStores::update($ID);
    }

    /**
     * Deletes a data store with "delete if exists" semantics.
     *
     * This method does not attempt to remove any intermediate and potentially
     * shared directories that may exist in its path.
     *
     * @param hex160 $ID
     *
     *      Because of the "delete if exists" semantics, this function will
     *      throw an exception if the ID is not a hex160 value to avoid
     *      situations where incorrect code believes something has been deleted
     *      when it hasn't.
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

        CBDataStores::deleteByID($ID);
    }

    /**
     * @return string
     */
    static function directoryForID($ID) {
        $directoryName = CBDataStore::directoryNameFromDocumentRoot($ID);

        return cbsitedir() . "/{$directoryName}";
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
     *
     *      This will usually be either CBSiteDirectory or CBSitePreferences::siteURL().
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
     * @deprecated use CBDataStore::create()
     *
     * @return null
     */
    static function makeDirectoryForID($ID) {
        CBDataStore::create($ID);
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
        $URL            = CBSitePreferences::siteURL() . "/{$directoryName}";

        if ($filename) {
            return "{$URL}/{$filename}";
        } else {
            return $URL;
        }
    }

    /**
     * This function takes a URI and converts it to a filepath if the file can
     * be confirmed to exist.
     *
     * @param string $URI
     *
     *      The URI can be in the following forms:
     *
     *      http://bob.com/dir/dir/file.ext
     *      /dir/dir/file.ext (relative to document root)
     *      /dir/dir/file.ext (absolute)
     *
     * @return ?string
     */
    static function URIToFilepath(string $URI): ?string {
        $path = parse_url($URI, PHP_URL_PATH);

        if (empty($path)) {
            return null;
        }

        if (is_file($path)) {
            return $path;
        }

        if (is_file(cbsitedir() . $path)) {
            return cbsitedir() . $path;
        }

        return null;
    }

    /**
     * Detects whether the URI represents a local data store and returns the
     * data store ID. The data store does not have to exist for the function to
     * return the ID.
     *
     * @param string $URI
     *
     *      Local URIs:
     *
     *      http://domain/data/.../
     *      https://domain/data/.../
     *      //domain/data/.../
     *      /data/.../
     *
     *      Absolute directory:
     *
     *      CBSiteDirectory/data/.../
     *
     * @return hex160|false
     *
     *      If the URI is local and references a data store the data store ID is
     *      returned; otherwise false.
     */
    static function URIToID($URI) {
        $components = parse_url($URI);

        if (!empty($components['host'])) {
            if ($components['host'] !== CBSitePreferences::siteDomainName()) {
                return false;
            }
        }

        if (empty($components['path'])) {
            return false;
        }

        $path = $components['path'];

        /**
         * Test for web addresses
         */

        $pattern = '%^/data/([0-9a-f]{2})/([0-9a-f]{2})/([0-9a-f]{36})/%';

        if (preg_match($pattern, $path, $matches)) {
            return "{$matches[1]}{$matches[2]}{$matches[3]}";
        }

        /**
         * Test for absolute directories
         */
        $siteDirectory = CBSiteDirectory;
        $pattern = "%^{$siteDirectory}/data/([0-9a-f]{2})/([0-9a-f]{2})/([0-9a-f]{36})/%";

        if (preg_match($pattern, $path, $matches)) {
            return "{$matches[1]}{$matches[2]}{$matches[3]}";
        }

        return false;
    }
}
