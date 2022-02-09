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

        /**
         * SCENARIO
         *
         * redirect URLs to primary domain
         */

        if (
            CBConfiguration::secondaryDomainsShouldRedirectToPrimaryDomain()
        ) {
            /**
             * If this functon decides to redirect it will also exit.
             */
            CBRequest::redirectSecondaryDomainsToPrimaryDomain();
        }

        $countOfStubs = count(
            ColbyRequest::$decodedStubs
        );


        /**
         * SCENARIO
         *
         * handle ajax requests
         */

        $isAjaxRequest = (
            $countOfStubs === 0 &&
            CBAjax::requestIsToCallAnAjaxFunction()
        );

        if (
            $isAjaxRequest
        ) {
            CBAjax::handleCallAjaxFunctionRequest();

            return;
        }


        /**
         * SCENARIO
         *
         * handle site setup
         *
         * @deprecated 2022_01_29
         *
         *      Websites are no longer set up using this method, they are set up
         *      fully using the cbt command in terminal. This code should be
         *      removed during a larger task to remove the old setup code.
         */

        if (
            CBSiteVersionNumber === 'setup' ||
            CBSiteIsConfigured === false
        ) {
            CBSetup::renderSetupPage();

            return;
        }

        /**
         * @NOTE 2022_01_29
         *
         *      Today this function was gardened fairly thoroughly in an effort
         *      to find a point at which we know that HTML is going to be
         *      rendered. The result of this effort was coming to the
         *      understanding that we can't know whether HTML is going to be
         *      rendered because handlers can render whatever they want and give
         *      no indication of what they will render.
         *
         *      In the future, this could be changed, but it's a pretty big
         *      change. The practical implication of this is that if you want to
         *      perform a task when any HTML is rendered then you should
         *      initiate that task from a JavaScript file that is included with
         *      as many HTML pages as possible. This concept will most likely be
         *      provided as a feature of Colby. (update this comment if
         *      availabe)
         *
         *      I'm pretty sure covering all HTML pages is theoretically
         *      impossible because Colby explicitly allows for HTML pages that
         *      do whatever they want, including using no JavaScript.
         */

         $canonicalEncodedPath = '';
         $renderOutputCallable = null;


        /**
         * SCENARIO
         *
         * front page
         */

        if (
            ColbyRequest::currentRequestIsForTheFrontPage()
        ) {
            $canonicalEncodedPath = '/';

            $renderOutputCallable = function() {
                CBRequest::setNoCacheHeaders();

                $frontPageID = CBSitePreferences::frontPageID();

                if (
                    isset($frontPageID)
                ) {
                    $model = CBModels::fetchModelByID(
                        $frontPageID
                    );

                    if (
                        !empty($model)
                    ) {
                        $model->title = CBSitePreferences::siteName();

                        CBPage::render(
                            $model
                        );

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
            $renderOutputCallable = function() {
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
            $renderOutputCallable = function() {
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
            $renderOutputCallable = function() {
                return include cbsysdir() .
                '/handlers/handle-sitemap.php';
            };
        }


        /**
         * SCENARIO
         *
         * find handler or page model based on the URL
         */

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
                $renderOutputCallable = function() use (
                    $allStubsHandlerFilepath
                ) {
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
                $renderOutputCallable = function() use (
                    $firstStubHandlerFilepath
                ) {
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

                    $renderOutputCallable = function() use (
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
            $renderOutputCallable
        ) {
            /**
             * We have a function to render the output, but if the URL is not
             * canonical we will redirect to the canonical URL instead.
             */

            if (
                ColbyRequest::$originalEncodedPath !== $canonicalEncodedPath
            ) {
                if (
                    false
                ) {
                    error_log( /* allowed */
                        ColbyRequest::$originalEncodedPath .
                        ' --> ' .
                        $canonicalEncodedPath
                    );
                }

                $redirectURI = (
                    $canonicalEncodedPath .
                    CBRequest::requestURIToOriginalEncodedQueryString()
                );

                header(
                    'Location: ' . $redirectURI,
                    true,
                    301
                );

                exit;
            }

            /**
             * The URL is canonical so we call the function to render the
             * output.
             */

            else {
                $renderOutputResult = call_user_func(
                    $renderOutputCallable
                );

                if (
                    $renderOutputResult === 1
                ) {
                    return;
                }
            }
        }


        /**
         * SCENARIO
         *
         * If we reach this point in the function either no page or handler
         * exists or the handler has declined to render any output.
         *
         * A scenario remains where the URL is for a resized version of a
         * CBImage and the file for that resized image hasn't yet been
         * generated.
         */

        $anImageWasMadeAndSent = CBImages::makeAndSendImageForPath(
            ColbyRequest::$decodedPath
        );

        if (
            $anImageWasMadeAndSent
        ) {
            return;
        }


        /**
         * SCENARIO
         *
         * 404
         */

        include Colby::findFile(
            'handlers/handle-default.php'
        );
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
