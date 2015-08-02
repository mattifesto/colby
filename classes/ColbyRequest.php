<?php

// TODO: Document official handler file naming a search policy in comments
//       in this file

final class ColbyRequest {

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

    /**
     * This function only returns the current iteration because it's needed
     * when the front page is being rendered. In the future, this process will
     * probably change.
     *
     * @return stdClass
     */
    public static function CBPagesRowForID($ID)
    {
        $IDAsSQL    = ColbyConvert::textToSQL($ID);
        $SQL        = <<<EOT

            SELECT
                `iteration`
            FROM
                `ColbyPages`
            WHERE
                `archiveID` = UNHEX('{$IDAsSQL}')

EOT;

        $result = Colby::query($SQL);
        $row    = $result->fetch_object();

        $result->free();

        return $row;
    }

    /**
     * @return stdClass
     */
    public static function CBPagesRowForURI($URI)
    {
        $URIAsSQL   = ColbyConvert::textToSQL($URI);
        $SQL        = <<<EOT

            SELECT
                LOWER(HEX(`archiveID`)) as `dataStoreID`,
                `className`,
                `iteration`,
                LOWER(HEX(`typeID`)) as `typeID`,
                LOWER(HEX(`groupID`)) as `groupID`
            FROM
                `ColbyPages`
            WHERE
                `URI` = '{$URIAsSQL}' AND
                `published` IS NOT NULL

EOT;

        $result = Colby::query($SQL);
        $row    = $result->fetch_object();

        $result->free();

        return $row;
    }

    ///
    ///
    ///
    public static function decodedStubs()
    {
        return self::$decodedStubs;
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
    public static function handleRequest() {
        $countOfStubs       = count(self::$decodedStubs);
        $handlerFilename    = null;

        // handle front page request

        if (0 === $countOfStubs)
        {
            $dataStore          = new CBDataStore(CBPageTypeID);
            $frontPageFilename  = $dataStore->directory() . '/front-page.json';

            if (file_exists($frontPageFilename)) {
                $frontPage  = json_decode(file_get_contents($frontPageFilename));
                $row        = self::CBPagesRowForID($frontPage->dataStoreID);

                CBViewPage::renderAsHTMLForID($frontPage->dataStoreID, $row->iteration);

                return;
            } else {
                /**
                 * This code is mostly deprecated. Not sure about first run scenarios.
                 */

                $handlerFilename = Colby::findHandler('handle-front-page.php');
            }
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

        /**
         * 2014.07.25
         * If a `robots.txt` file actually exists in the web root directory
         * this code path will never be taken. Otherwise, default `robots.txt`
         * implementations are provided which can be overridden by placing
         * replacements in the main website's `setup` directory.
         */
        else if (1 === $countOfStubs &&
                 'robots.txt' === self::$decodedStubs[0])
        {
            if (defined('CBShouldDisallowRobots') &&
                CBShouldDisallowRobots)
            {
                readfile(Colby::findFile('setup/robots_disallow.txt'));
            }
            else
            {
                readfile(Colby::findFile('setup/robots_allow.txt'));
            }

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
                $URI        = implode('/', self::$decodedStubs);
                $row        = self::CBPagesRowForURI($URI);

                if ($row) {
                    self::canonicalizeRequestURI();

                    $className = $row->className;

                    if (is_callable($function = "{$className}::renderAsHTMLForID")) {
                        call_user_func($function, $row->dataStoreID, $row->iteration);
                    } else {
                        /* Deprecated */
                        $page = $className::initWithID($row->dataStoreID);
                        $page->renderHTML();
                    }

                    return;
                }
            }
        }

        if ($handlerFilename) {

            /**
             * At this point we know that this is a valid set of stubs but the
             * URL may not be canonical. Calling `canonicalizeRequestURI` will
             * return if the URI is canonical or it will redirect and end the
             * process if the URI isn't canonical.
             */

            self::canonicalizeRequestURI();

            $result = include($handlerFilename);

            if (1 === $result) {

                /**
                 * The handler file was included successfully so we're done.
                 */

                return;
            }
        }

        /**
         * Either no valid handler was found or the handler returned a value
         * indicating that the page wasn't found.
         */

        include Colby::findHandler('handle-default.php');
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

    /**
     * @return bool
     *  Returns `true` if the current request is for the front page.
     */
    public static function isForFrontPage()
    {
        return !(count(self::$decodedStubs));
    }
}

ColbyRequest::initialize();
