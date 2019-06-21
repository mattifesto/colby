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
     * @param string $ID
     *
     * @return int
     */
    static function currentIterationForPageID(string $ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT  `iteration`
            FROM    `ColbyPages`
            WHERE   `archiveID` = {$IDAsSQL}

EOT;

        return CBDB::SQLToValue($SQL);
    }
    /* currentIterationForPageID() */


    /**
     * @return [{string}]
     */
    static function decodedStubs() {
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
     * @return null
     */
    static function handleRequest() {
        $canonicalEncodedPath = '';
        $countOfStubs = count(ColbyRequest::$decodedStubs);
        $function = null;

        if (0 === $countOfStubs && CBAjax::call()) {
            return;
        }

        if ((0 === $countOfStubs) ||
            (1 === $countOfStubs && 'index.php' === self::$decodedStubs[0]))
        {
            $canonicalEncodedPath = '/';
            $function = function() {
                $frontPageID = CBSitePreferences::frontPageID();

                if (isset($frontPageID)) {
                    $model = CBModels::fetchModelByID($frontPageID);

                    if (!empty($model)) {
                        $model->title = CBSitePreferences::siteName();
                        CBPage::render($model);
                        return 1;
                    }
                }

                return include Colby::findFile('handlers/handle-front-page.php');
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

            if ($allStubsHandlerFilepath = Colby::findFile("handlers/handle,{$allStubs}.php")) {
                $function = function() use ($allStubsHandlerFilepath) {
                    return include $allStubsHandlerFilepath;
                };
            } else if ($firstStubHandlerFilepath = Colby::findFile("handlers/handle,{$firstStub},.php")) {
                $function = function() use ($firstStubHandlerFilepath) {
                    return include $firstStubHandlerFilepath;
                };
            } else {
                $URIForSearch = implode('/', self::$decodedStubs);
                $row = ColbyRequest::pageRenderingDataForURI($URIForSearch);

                if ($row) {
                    $canonicalEncodedPath = CBRequest::decodedPathToCanonicalEncodedPath($row->URI);

                    if ($row->model) {
                        $function = function() use ($row) {
                            CBPage::render($row->model);
                            return 1;
                        };
                    }
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
         * If the path provided is for an image that exists already and for an
         * approved automatic resize, then resize the image and send it back.
         */
        if (CBImages::makeAndSendImageForPath(ColbyRequest::$decodedPath)) {
            return;
        }

        /**
         * Either no page was found or the function returned a value other than
         * 1 which indicates that the page doesn't exist.
         */

        include Colby::findFile('handlers/handle-default.php');
    }

    /**
     * @return null
     */
    static function initialize() {
        self::$originalEncodedPath = CBRequest::requestURIToOriginalEncodedPath();
        self::$decodedPath = urldecode(self::$originalEncodedPath);
        self::$decodedStubs = CBRequest::decodedPathToDecodedStubs(self::$decodedPath);
        self::$encodedStubs = array_map('urlencode', self::$decodedStubs);
    }

    /**
     * @return bool
     *  Returns `true` if the current request is for the front page.
     */
    static function isForFrontPage()
    {
        return !(count(self::$decodedStubs));
    }

    /**
     * @return object|false
     */
    private static function pageRenderingDataForURI($URI) {
        $URIAsSQL   = CBDB::stringToSQL($URI);
        $SQL        = <<<EOT

            SELECT      LOWER(HEX(`p`.`archiveID`)) as `ID`,
                        `p`.`className`,
                        `p`.`iteration`,
                        `v`.`modelAsJSON` as `model`,
                        `p`.`URI`
            FROM        `ColbyPages`        AS `p`
            LEFT JOIN   `CBModels`          AS `m` ON `p`.`archiveID` = `m`.`ID`
            LEFT JOIN   `CBModelVersions`   AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE       `p`.`URI` = {$URIAsSQL} AND
                        `p`.`published` IS NOT NULL
            ORDER BY    `p`.`published` ASC
            LIMIT       1

EOT;

        $data = CBDB::SQLToObject($SQL);

        if ($data !== false) {
            $data->model = json_decode($data->model);
        }

        return $data;
    }
}

ColbyRequest::initialize();
