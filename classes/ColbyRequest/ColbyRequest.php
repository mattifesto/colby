<?php

/**
 * @deprecated use CBRequest
 *
 *      Move the functionality of this file into the CBRequest class.
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
     * @return bool
     */
    static function currentRequestIsForTheFrontPage(): bool {
        $countOfStubs = count(ColbyRequest::decodedStubs());

        if ($countOfStubs === 0) {
            return true;
        }

        if ($countOfStubs === 1) {
            $stub = ColbyRequest::decodedStubs()[0];

            return $stub === 'index.php';
        }

        return false;
    }
    /* currentRequestIsForTheFrontPage() */


    /**
     * @return [string]
     */
    static function decodedStubs() {
        return ColbyRequest::$decodedStubs;
    }
    /* decodedStubs() */


    /**
     * Most sites will call this method from /index.php to handle requests in
     * the standard way by doing one of the following:
     *
     *      Handle Ajax request.
     *
     *      Search for the appropriate handler file and load it.
     *
     *      Handle well known URLs, such as robots.txt.
     *
     *      Search for the URL in the pages table.
     *
     *      Display the 404 page.
     *
     * @return void
     */
    static function handleRequest(): void {
        $canonicalEncodedPath = '';
        $countOfStubs = count(ColbyRequest::$decodedStubs);
        $function = null;

        if (
            0 === $countOfStubs &&
            CBAjax::requestIsToCallAnAjaxFunction()
        ) {
            CBAjax::handleCallAjaxFunctionRequest();
            return;
        }

        if (ColbyRequest::currentRequestIsForTheFrontPage()) {
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

                return include Colby::findFile(
                    'handlers/handle-front-page.php'
                );
            };
        }

        else if (
            1 === $countOfStubs &&
            'robots.txt' === ColbyRequest::$decodedStubs[0]
        ) {
            $canonicalEncodedPath = '/robots.txt';
            $function = function() {
                return include cbsysdir() .
                '/handlers/handle-robots.php';
            };
        }

        else if (
            1 === $countOfStubs &&
            'sitemap.xml' === ColbyRequest::$decodedStubs[0]
        ) {
            $canonicalEncodedPath = '/sitemap.xml';
            $function = function() {
                return include cbsysdir() .
                '/handlers/handle-sitemap.php';
            };
        }

        else {
            $canonicalEncodedPath = implode('/', ColbyRequest::$encodedStubs);
            $canonicalEncodedPath = "/{$canonicalEncodedPath}/";
            $allStubs = implode(',', ColbyRequest::$encodedStubs);
            $firstStub = ColbyRequest::$encodedStubs[0];

            if (
                $allStubsHandlerFilepath = Colby::findFile(
                    "handlers/handle,{$allStubs}.php"
                )
            ) {
                $function = function() use ($allStubsHandlerFilepath) {
                    return include $allStubsHandlerFilepath;
                };
            }

            else if (
                $firstStubHandlerFilepath = Colby::findFile(
                    "handlers/handle,{$firstStub},.php"
                )
            ) {
                $function = function() use ($firstStubHandlerFilepath) {
                    return include $firstStubHandlerFilepath;
                };
            }

            else {
                $row = ColbyRequest::pageRenderingDataForURI(
                    implode(
                        '/',
                        ColbyRequest::$decodedStubs
                    )
                );

                if ($row) {
                    $canonicalEncodedPath =
                    CBRequest::decodedPathToCanonicalEncodedPath($row->URI);

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
            if (ColbyRequest::$originalEncodedPath !== $canonicalEncodedPath) {
                $redirectURI =
                $canonicalEncodedPath .
                CBRequest::requestURIToOriginalEncodedQueryString();

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
    /* handleRequest() */


    /**
     * @return void
     */
    static function initialize(): void {
        ColbyRequest::$originalEncodedPath =
        CBRequest::requestURIToOriginalEncodedPath();

        ColbyRequest::$decodedPath =
        urldecode(ColbyRequest::$originalEncodedPath);

        ColbyRequest::$decodedStubs =
        CBRequest::decodedPathToDecodedStubs(ColbyRequest::$decodedPath);

        ColbyRequest::$encodedStubs = array_map(
            'urlencode',
            ColbyRequest::$decodedStubs
        );
    }
    /* initialize() */


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
            LEFT JOIN   `CBModelVersions`   AS `v` ON `m`.`ID` = `v`.`ID` AND
                                                      `m`.`version` = `v`.`version`
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
    /* pageRenderingDataForURI() */
}
/* ColbyRequest */


ColbyRequest::initialize();
