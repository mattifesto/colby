<?php

/**
 * @deprecated use CBRequest
 *
 *      Move the functionality of this file into the CBRequest class.
 */
final class
ColbyRequest {

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
        $IDAsSQL = CBID::toSQL($ID);
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
     *      Redirect to a URL using the primary domain.
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
    static function
    handleRequest(
    ): void {
        if (
            CBConfiguration::secondaryDomainsShouldRedirectToPrimaryDomain()
        ) {
            CBRequest::redirectSecondaryDomainsToPrimaryDomain();
        }

        $canonicalEncodedPath = '';
        $countOfStubs = count(ColbyRequest::$decodedStubs);
        $function = null;

        $isAjaxRequest = (
            $countOfStubs === 0 &&
            CBAjax::requestIsToCallAnAjaxFunction()
        );

        if ($isAjaxRequest) {

            /**
             * CBAjax::handleCallAjaxFunctionRequest() will set the exception
             * hander to a function that will respond with an Ajax response.
             */
            CBAjax::handleCallAjaxFunctionRequest();

            return;
        }

        if (
            CBSiteVersionNumber === 'setup' ||
            CBSiteIsConfigured === false
        ) {
            CBSetup::renderSetupPage();

            return;
        }

        /**
         * @NOTE 2019_12_09
         *
         *      At this point we can't quite assume that the request is
         *      expecting an HTML response, but should probably set an exception
         *      handler that will respond with HTML anyway.
         */

        /* front page */

        if (ColbyRequest::currentRequestIsForTheFrontPage()) {
            $canonicalEncodedPath = '/';

            $function = function() {
                CBRequest::setNoCacheHeaders();

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


        /* ads.txt */

        else if (
            1 === $countOfStubs &&
            'ads.txt' === ColbyRequest::$decodedStubs[0]
        ) {
            $canonicalEncodedPath = '/ads.txt';
            $function = function() {
                return include cbsysdir() .
                '/handlers/handle-ads.php';
            };
        }



        /* robots.txt */

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


        /* sitemap.xml */

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


        /* interpret URI */

        else {
            $canonicalEncodedPath = implode(
                '/',
                ColbyRequest::$encodedStubs
            );

            $canonicalEncodedPath = "/{$canonicalEncodedPath}/";

            $allStubs = implode(
                ',',
                ColbyRequest::$encodedStubs
            );

            $firstStub = ColbyRequest::$encodedStubs[0];


            /* all stubs handler */

            if (
                $allStubsHandlerFilepath = Colby::findFile(
                    "handlers/handle,{$allStubs}.php"
                )
            ) {
                $function = function() use ($allStubsHandlerFilepath) {
                    CBRequest::setNoCacheHeaders();
                    return include $allStubsHandlerFilepath;
                };
            }


            /* first stub handler */

            else if (
                $firstStubHandlerFilepath = Colby::findFile(
                    "handlers/handle,{$firstStub},.php"
                )
            ) {
                $function = function() use ($firstStubHandlerFilepath) {
                    CBRequest::setNoCacheHeaders();
                    return include $firstStubHandlerFilepath;
                };
            }


            /* page model */

            else {
                $URI = implode(
                    '/',
                    ColbyRequest::$decodedStubs
                );

                $pageModel = ColbyRequest::fetchPageModelForURI(
                    $URI
                );

                if (
                    $pageModel !== null
                ) {
                    $canonicalEncodedPath = (
                        CBRequest::decodedPathToCanonicalEncodedPath(
                            $URI
                        )
                    );

                    $function = function() use (
                        $pageModel
                    ) {
                        CBRequest::setNoCacheHeaders();

                        CBPage::render(
                            $pageModel
                        );

                        return 1;
                    };
                }
            }
        }

        if (
            $function
        ) {
            if (ColbyRequest::$originalEncodedPath !== $canonicalEncodedPath) {
                $redirectURI = (
                    $canonicalEncodedPath .
                    CBRequest::requestURIToOriginalEncodedQueryString()
                );

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
    static function
    initialize(
    ): void {
        /**
         * If this function is called from a process initiated from the comman
         * line interface there is conceptually no work for this class to do.
         */
        if (php_sapi_name() === 'cli') {
            return;
        }

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
     * @param string $URI
     *
     * @return object|null
     */
    private static function
    fetchPageModelForURI(
        string $URI
    ): ?stdClass {
        $URLPath = "/{$URI}/";

        $pageModelCBID = CBModels::fetchCBIDByURLPath(
            $URLPath
        );

        if (
            $pageModelCBID === null
        ) {
            $potentialPageModelCBIDs = CBPages::fetchPublishedPageIDsByURI(
                $URI
            );

            if (
                count($potentialPageModelCBIDs) > 0
            ) {
                $pageModelCBID = $potentialPageModelCBIDs[0];
            }
        }

        if (
            $pageModelCBID === null
        ) {
            return null;
        }

        return CBModels::fetchModelByCBID(
            $pageModelCBID
        );
    }
    /* fetchPageModelForURI() */

}
/* ColbyRequest */


ColbyRequest::initialize();
