<?php

class ColbyRequest
{
    private static $requestedURL;
    private static $requestedQueryString;

    private static $stubs;

    ///
    ///
    ///
    public static function canonicalizeURL()
    {
        $canonicalURL = '/';

        if (count(self::$stubs) === 1 && 'index.php' === self::$stubs[0])
        {
            // canonical URL is still '/'
        }
        else if (count(self::$stubs) > 0)
        {
            $canonicalURL = '/' . implode('/', self::$stubs) . '/';
        }

        if (self::$requestedURL !== $canonicalURL)
        {
            $redirectURI = $canonicalURL . self::$requestedQueryString;

            header('Location: ' . $redirectURI, true, 301);
            exit;
        }
    }

    ///
    /// this function should be run only once
    /// it is run automatically when ColbyRequest is first included
    ///
    public static function initialize()
    {
        // step 1: separate url from query string

        // 1: url
        // 2: query string (optional)

        preg_match('/^(.*?)(\?.*)?$/',
            $_SERVER['REQUEST_URI'],
            $matches);

        self::$requestedURL = urldecode($matches[1]);

        if (isset($matches[2]))
        {
            self::$requestedQueryString = $matches[2];
        }
        else
        {
            self::$requestedQueryString = '';
        }

        // step 2: get stubs
        // preg_split will return an empty array if there aren't any stubs

        self::$stubs = preg_split('/[\/\s]+/',
            self::$requestedURL,
            null,
            PREG_SPLIT_NO_EMPTY);

        // step 3: handle reserved stubs

        if (1 === count(self::$stubs))
        {
            switch (self::$stubs[0])
            {
                case 'facebook-oauth-handler':

                    // this page redirects
                    // so we can exit after

                    require(COLBY_SITE_DIRECTORY .
                        '/colby/pages/facebook-oauth-handler.php');
                    exit;

                case 'logout':

                    // this page redirects
                    // so we can exit after

                    require(COLBY_SITE_DIRECTORY .
                        '/colby/pages/logout.php');
                    exit;

                default:

                    break;
            }
        }
    }

    ///
    ///
    ///
    public static function requestedURL()
    {
        return self::$requestedURL;
    }

    ///
    ///
    ///
    public static function stubs()
    {
        return self::$stubs;
    }
}

ColbyRequest::initialize();
