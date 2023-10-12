<?php

/**
 * This class is responsible for creating, locating, and deleting Colby data
 * stores.
 *
 * ! This class is a limited functionality core system class. It's meant to be
 * ! simple, obvious, and non-controversial without any non-obvious or tricky
 * ! logic. Any potential additions to the class should be highly scrutinized.
 *
 * 2014_01_29 Don't bother attempting to provide locking functionality to this
 * class or any PHP code unless it is database related. The file locking
 * functionality of PHP isn't very certain and that fact is well documented.
 */
final class
CBDataStore
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v480.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    // -- functions



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
        if (!CBID::valueIsCBID($ID)) {
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
    /* directoryForID() */


    /**
     * @param string $ID
     *      example: "1ab9879ccb12eaaeda7b81b08fa433fde8bc86e3"
     *
     * @return string
     *      example: "data/1a/b9/879ccb12eaaeda7b81b08fa433fde8bc86e3"
     */
    static function directoryNameFromDocumentRoot($ID) {

        /**
         * @NOTE 2019_06_15
         *
         *      We don't allow correction of capital letters in an ID and I'm
         *      not sure why we're doing it here. At some point investigate
         *      further.
         */
        $ID = strtolower($ID);

        $directoryName = preg_replace('/^(..)(..)/', '$1/$2/', $ID);

        return "data/{$directoryName}";
    }
    /* directoryNameFromDocumentRoot() */


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
     * @param CBID $ID
     * @param ?string $basename
     * @param ?string $flexdir
     *
     *      This argument will usually hold the value returned by either
     *      cb_document_root_directory() or cbsiteurl().
     *
     *      @NOTE 2023-07-15
     *      Matt Calkins
     *
     *      Recently cbsiteurl() was altered to return "" instead of something
     *      like "http://example.com" purposely to cause many of the URLs
     *      generated by Colby to become root relative URLs. This change was
     *      made to make Colby work better in containerized situations. This
     *      change resulted in an issue with this function and similar functions
     *      because this function did not see a difference between a $flexdir
     *      argument value of "" vs null. The function was changed to interpret
     *      a $flexdir value of "" to mean "I am providing you with a flexdir
     *      and it is an empty string so please return an absolute path." A
     *      $flexdir argument value of null means, "I am not giving you a
     *      $flexdir argument so please return a relative path."
     *
     * @return string
     */
    static function
    flexpath(
        $ID,
        $basename = null,
        $flexdir = null
    ): string
    {
        $flexpath =
        CBDataStore::directoryNameFromDocumentRoot(
            $ID
        );

        if (
            !empty($basename)
        ) {
            $flexpath =
            "{$flexpath}/{$basename}";
        }

        if (
            $flexdir !== null
        ) {
            $flexpath =
            "{$flexdir}/{$flexpath}";
        }

        return $flexpath;
    }
    // flexpath()



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

        $directoryName = CBDataStore::directoryNameFromDocumentRoot($ID);
        $URL = cbsiteurl() . "/{$directoryName}";

        if ($filename) {
            return "{$URL}/{$filename}";
        } else {
            return $URL;
        }
    }



    /**
     * This function takes a URL and converts it to a filepath if the file can
     * be confirmed to exist.
     *
     * @param string $URL
     *
     *      This function has "URI" in it from a time when we were using that
     *      term instead of URL and would be named using URL now. This function
     *      only accepts URLs with a domain or absolute URLS. It would not know
     *      what context to user if given a relative URL. The URL can be in the
     *      following forms:
     *
     *      http://bob.com/dir/dir/file.ext
     *      /dir/dir/file.ext
     *
     * @return ?string
     *
     *      This function will return an absolute path to an existing file or
     *      null.
     */
    static function
    URIToFilepath(
        string $URL
    ): ?string
    {
        $path =
        parse_url(
            $URL,
            PHP_URL_PATH
        );

        if (
            empty($path)
        ) {
            return null;
        }

        $firstCharacterOfPath =
        mb_substr(
            $path,
            0,
            1
        );

        if (
            $firstCharacterOfPath !== '/'
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    A relative URL was passed as the \$URL argument when calling
                    CBDataStore::URIToFilepath(). The URL argument of
                    CBDataStore::URIToFilepath() must be an absolute URL because
                    the function does not have the context to interpret relative
                    URLs.

                EOT),
                $URL,
                'ad0bfbb5d9459185107cf3b158c3b3d0209c7f2d'
            );
        }

        $absoluteFilePath =
        cbsitedir() . $path;

        if (
            is_file($absoluteFilePath)
        ) {
            return $absoluteFilePath;
        }

        return null;
    }
    /* URIToFilepath() */



    /**
     * This function will return the CBID contained in any string that has a
     * valid "/data/.../" data store path. This includes URLs and file system
     * paths.
     *
     * @param string $dataStoreURL
     *
     * @return CBID|null
     */
    static function
    URLToCBID(
        string $dataStoreURL
    ): ?string {
        $pattern = (
            '%/data/([0-9a-f]{2})/([0-9a-f]{2})/([0-9a-f]{36})/%'
        );

        $matchWasFound = preg_match(
            $pattern,
            $dataStoreURL,
            $matches
        );

        if ($matchWasFound) {
            return "{$matches[1]}{$matches[2]}{$matches[3]}";
        }

        return null;
    }
    /* URLToCBID() */



    /**
     * @deprecated use CBDataStore::URLToCBID()
     *
     *      2023-07-18
     *      Matt Calkins
     *
     *      This function used to do some types of verification of the URL that
     *      were not very effective and not necessarily correct. If you need to
     *      know if the CBID returned from this function is truly valid, see if
     *      a model with the CBID exists in the database.
     *
     * @param string $URI
     *
     *      Examples:
     *
     *      http://domain/data/.../
     *      https://domain/data/.../
     *      //domain/data/.../
     *      /data/.../
     *      .../data/.../
     *
     * @return CBID|false
     */
    static function
    URIToID(
        $URI
    ) {
        $CBID =
        CBDataStore::URLToCBID(
            $URI
        );

        if (
            $CBID === null
        ) {
            return false;
        }

        return $CBID;
    }
    /* URIToID() */

}
