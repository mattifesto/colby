<?php

// TODO: Document official handler file naming a search policy in comments
//       in this file

class ColbyRequest
{
    /**
     * If handling the request involves displaying a document then `$archive`
     * will be set to the document's archive before the document type's view is
     * included.
     */
    public static $archive = null;

    private static $decodedRequestURI;
    // type: stríng
    // example:
    // /foo bar/piñata/post/

    private static $requestQueryString;
    // type: string
    // example:
    // ?user=bob+jones

    private static $encodedStubs;
    // type: array
    // example:
    // foo+bar, pi%C3%B1ata, post

    private static $decodedStubs;
    // type: array
    // example:
    // foo bar, piñata, post

    ///
    /// construct a canonical URI using decoded stubs
    /// compare this agains a decoded request URI
    /// if the don't  match then
    /// 1) construct a canonical URI using encoded data
    /// 2) append the original query string
    /// 3) and finally redirect the browser to the canonical URL
    ///
    public static function canonicalizeRequestURI()
    {
        $countOfDecodedStubs = count(self::$decodedStubs);
        $canonicalDecodedURI = '/';

        // construct cononical URI using decoded stubs

        if (   1 === $countOfDecodedStubs
            && 'index.php' === self::$decodedStubs[0])
        {
            // canonical URI is still '/'
        }
        else if ($countOfDecodedStubs > 0)
        {
            $canonicalDecodedURI = '/' .
                implode('/', self::$decodedStubs) .
                '/';
        }

        if (self::$decodedRequestURI !== $canonicalDecodedURI)
        {
            // 1) construct a canonical URI using encoded data

            if ('/' === $canonicalDecodedURI)
            {
                $canonicalEncodedURI = '/';
            }
            else
            {
                $canonicalEncodedURI = '/' .
                    implode('/', self::$encodedStubs) .
                    '/';
            }

            // 2) append the original query string

            $redirectURI = $canonicalEncodedURI .
                self::$requestQueryString;

            // 3) and finally redirect the browser to the canonical URL

            header('Location: ' . $redirectURI, true, 301);
            exit;
        }
    }

    ///
    ///
    ///
    public static function decodedStubs()
    {
        return self::$decodedStubs;
    }

    /**
     * @return ColbyArchive | null
     *
     * If the $urlPath argument represents a page that can be rendered using
     * a view and an archive, the archive will be returned.
     */
    public static function archiveForURI($uri)
    {
        $archive = null;

        $uri = Colby::mysqli()->escape_string($uri);

        $sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`
FROM
    `ColbyPages`
WHERE
    `stub` = '{$uri}' AND
    `modelId` IS NOT NULL AND
    `published` IS NOT NULL
EOT;

        $result = Colby::query($sql);

        if ($result->num_rows != 1) // This will either be 1 or 0.
        {
            goto done;
        }

        $archiveId = $result->fetch_object()->archiveId;

        $archive = ColbyArchive::open($archiveId);

        done:

        $result->free();

        return $archive;
    }

    /**
     * It's not required that this method be called
     * however most sites will call this method from index.php
     * to handle requests in the standard way
     * which is to search for the appropriate handler file and load it.
     * If no handler file is found, the method will search for the URL
     * in the database to see if it can load it that way.
     *
     * @return void
     */
    public static function handleRequest()
    {
        $archiveId = null;
        $countOfStubs = count(self::$decodedStubs);
        $handlerFilename = null;

        // handle front page request

        if (0 === $countOfStubs)
        {
            $handlerFilename = Colby::findHandler('handle-front-page.php');
        }

        // redirect requests for
        //   http://example.com/index.php
        // to
        //   http://example.com/

        else if (   1 === $countOfStubs
                 && 'index.php' === self::$decodedStubs[0])
        {
            self::canonicalizeRequestURI();

            // canonicalizeRequestURI() will exit for us
            // because the URL definitely needs to change in this case
            // the exit here should never be reached
            // but it's place here to make intentions clear

            exit;
        }

        // search for handler files
        // handler filenames use encoded stubs
        // (no spaces or special characters)
        //
        // 1. check for "every stub" URL handler
        // 2. check for "first stub" multi-URL handler
        // 3. Check whether page is displayable without a stub related handler.

        else
        {
            // 1. check for "every stub" URL handler

            $stubsPart = implode(',', self::$encodedStubs);

            $handlerFilename = Colby::findHandler("handle,{$stubsPart}.php");

            // 2. check for "first stub" multi-URL handler

            if (!$handlerFilename)
            {
                $firstStub = self::$encodedStubs[0];

                $handlerFilename = Colby::findHandler("handle,{$firstStub},.php");
            }

            // 3. Check whether page is displayable without a stub related handler.

            if (!$handlerFilename && COLBY_MYSQL_DATABASE)
            {
                $uri = implode('/', self::$decodedStubs);

                /**
                 * 2014.01.07
                 * TODO:
                 *
                 * The web page type id should be retrieved from the database
                 * and used in the code below before the page archive is
                 * loaded. The page archive may not even be needed.
                 * Furthermore, the official location of the page type id
                 * is the database. The type id in the archive is just for
                 * reference if the archive is ever orphaned. If these two
                 * values are different, the value in the database has higher
                 * priority.
                 */

                self::$archive = self::archiveForURI($uri);

                if (self::$archive)
                {
                    $documentGroupId = self::$archive->valueForKey('documentGroupId');
                    $documentTypeId = self::$archive->valueForKey('documentTypeId');

                    if (CBSectionedPageTypeId == $documentTypeId)
                    {
                        /**
                         * 2014.01.08
                         * TODO:
                         *
                         * The CBSectionedPageTypeId is handled explicitly
                         * here and probably should be since it may be the only
                         * document type ever used.
                         *
                         * However, in the else block below the code should
                         * be changed to use a site defined array of web page
                         * type ids and handlers because the "document" methods
                         * and tools for custom page types will be deprecated.
                         */

                        $handlerFilename = CBSystemDirectory . '/handlers/handle-sectioned-page.php';
                    }
                    else
                    {
                        $handlerFilename = Colby::findFileForDocumentType(
                            'view.php', $documentGroupId, $documentTypeId);
                    }
                }
            }
        }

        if ($handlerFilename)
        {
            // this is a valid set of stubs but the URL may not be canonical
            // calling canonicalizeRequestURI will return if the URI
            // is canonical or it will redirect and end the process
            // if the URI isn't canonical

            self::canonicalizeRequestURI();

            $result = include($handlerFilename);

            if (1 === $result)
            {
                // file was included and did not return a special value
                // we take this to mean everything went fine and we're done

                return;
            }
        }

        // Either no valid handler was found or the handler returned a value
        // indicating that the page wasn't found.

        include(Colby::findHandler('handle-default.php'));
    }

    ///
    /// this function should be run only once
    /// it is run automatically when ColbyRequest is first included
    ///
    public static function initialize()
    {
        // step 1: separate url from query string
        //
        // $matches[1]: encoded request URI
        // $matches[2]: query string (may or may not be present)

        preg_match('/^(.*?)(\?.*)?$/',
            $_SERVER['REQUEST_URI'],
            $matches);

        // step 2: decode request URI

        self::$decodedRequestURI = urldecode($matches[1]);

        if (isset($matches[2]))
        {
            self::$requestQueryString = $matches[2];
        }
        else
        {
            self::$requestQueryString = '';
        }

        // step 3: get decoded stubs
        //
        // note: PREG_SPLIT_NO_EMPTY
        //       this will prevent preg_split from returning empty stubs
        //       from before the first and after the last slash
        //
        // note: repeated slashes are treated as one: '[\/]+'
        //       if there are repeated slashes the URL is not canonical
        //       and will be rewritten
        //
        // preg_split will return an empty array if there aren't any stubs

        self::$decodedStubs = preg_split('/[\/]+/',
            self::$decodedRequestURI,
            null,
            PREG_SPLIT_NO_EMPTY);

        // step 4: re-encode stubs
        //
        // This is necessary because while the URI comes to us encoded
        // it is not always fully encoded. For instance, often commas will not
        // be encoded. Re-encoding the stubs canonicalizes the encoding so
        // that our stubs are fully encoded the same way every time
        // regardless of how we receive them.

        self::$encodedStubs = array();

        foreach (self::$decodedStubs as $decodedStub)
        {
            self::$encodedStubs[] = urlencode($decodedStub);
        }
    }
}

ColbyRequest::initialize();
