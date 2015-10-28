<?php

/**
 * @deprecated use CBRequest
 *  Move the functionality of this file into the CBRequest class.
 */
final class ColbyRequest {

    private static $decodedPath;
    // type: stríng
    // example:
    // /foo bar/piñata/post/

    private static $decodedStubs;
    // type: array
    // example:
    // foo bar, piñata, post

    private static $encodedStubs;
    // type: array
    // example:
    // foo%20bar, pi%C3%B1ata, post

    private static $originalEncodedPath;
    // type: stríng
    // example:
    // /foo+bar/pi%C3%B1ata/post/

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
     * @return {stdClass} | false
     */
    public static function CBPagesRowForURI($URI) {
        $URIAsSQL   = CBDB::stringToSQL($URI);
        $SQL        = <<<EOT

            SELECT      LOWER(HEX(`archiveID`)) as `ID`,
                        `className`,
                        `iteration`,
                        `URI`
            FROM        `ColbyPages`
            WHERE       `URI` = {$URIAsSQL} AND
                        `published` IS NOT NULL
            ORDER BY    `published` ASC
            LIMIT       1

EOT;

        return CBDB::SQLToObject($SQL);
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
        $canonicalEncodedPath = '';
        $countOfStubs = count(self::$decodedStubs);
        $function = null;

        if ((0 === $countOfStubs) ||
            (1 === $countOfStubs && 'index.php' === self::$decodedStubs[0]))
        {
            $canonicalEncodedPath = '/';
            $function = function() {
                $filepath = CBDataStore::filepath([
                    'ID' => CBPageTypeID,
                    'filename' => 'front-page.json'
                ]);

                if (file_exists($filepath)) {
                    $frontPage  = json_decode(file_get_contents($filepath));
                    $row        = ColbyRequest::CBPagesRowForID($frontPage->dataStoreID);

                    CBViewPage::renderAsHTMLForID($frontPage->dataStoreID, $row->iteration);
                    return 1;
                } else {
                    return include Colby::findHandler('handle-front-page.php');
                }
            };
        } else if (1 === $countOfStubs && 'robots.txt' === self::$decodedStubs[0]) {
            $canonicalEncodedPath = '/robots.txt';
            $function = function() {
                return include CBSystemDirectory . '/handlers/handle-robots.php';
            };
        } else if (1 === $countOfStubs && 'sitemap.xml' === self::$decodedStubs[0]) {
            $canonicalEncodedPath = '/sitemap.xml';
            $function = function() {
                return include CBSystemDirectory . '/handlers/handle-sitemap.php';
            };
        } else {
            $canonicalEncodedPath = implode('/', self::$encodedStubs);
            $canonicalEncodedPath = "/{$canonicalEncodedPath}/";
            $allStubs = implode(',', self::$encodedStubs);
            $firstStub = self::$encodedStubs[0];

            if ($allStubsHandlerFilepath = Colby::findHandler("handle,{$allStubs}.php")) {
                $function = function() use ($allStubsHandlerFilepath) {
                    return include $allStubsHandlerFilepath;
                };
            } else if ($firstStubHandlerFilepath = Colby::findHandler("handle,{$firstStub},.php")) {
                $function = function() use ($firstStubHandlerFilepath) {
                    return include $firstStubHandlerFilepath;
                };
            } else {
                $URIForSearch = implode('/', self::$decodedStubs);
                $row = ColbyRequest::CBPagesRowForURI($URIForSearch);

                if ($row && is_callable("{$row->className}::renderAsHTMLForID")) {
                    $canonicalEncodedPath = CBRequest::decodedPathToCanonicalEncodedPath($row->URI);
                    $function = function() use ($row) {
                        call_user_func("{$row->className}::renderAsHTMLForID", $row->ID, $row->iteration);
                        return 1;
                    };
                }
            }
        }

        if ($function) {
            if (self::$originalEncodedPath !== $canonicalEncodedPath) {
                $redirectURI = $canonicalEncodedPath . CBRequest::requestURIToOriginalEncodedQueryString();
                header('Location: ' . $redirectURI, true, 301);
                exit;
            } else {
                if (call_user_func($function) === 1) {
                    return;
                }
            }
        }

        /**
         * Either no page was found or the function returned a value other than
         * 1 which indicates that the page doesn't exist.
         */

        include Colby::findHandler('handle-default.php');
    }

    /**
     * @return null
     */
    public static function initialize() {
        self::$originalEncodedPath = CBRequest::requestURIToOriginalEncodedPath();
        self::$decodedPath = urldecode(self::$originalEncodedPath);
        self::$decodedStubs = CBRequest::decodedPathToDecodedStubs(self::$decodedPath);
        self::$encodedStubs = array_map('urlencode', self::$decodedStubs);
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
