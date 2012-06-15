<?php

class ColbyRequest
{
    private static $decodedRequestURI;
    // example:
    // /foo bar/piñata/post/

    private static $requestedQueryString;
    // example:
    // ?user=bob+jones

    private static $encodedStubs;
    // example:
    // foo+bar, pi%C3%B1ata, post

    private static $decodedStubs;
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
                self::$requestedQueryString;

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

    ///
    /// it's not required that this function be called
    /// however most sites will call this function from index.php
    /// to handle requests in the standard way
    /// which is to search for the appropriate hanlder file and load it
    ///
    public static function handleRequest()
    {
        $countOfStubs = count(self::$decodedStubs);
        $handlerPath = null;

        // handle front page request

        if (0 === $countOfStubs)
        {
            $fileName = 'handle-special-front-page.php';

            $handlerPath = COLBY_SITE_DIRECTORY .
                '/handlers/' .
                $fileName;

            if (!is_file($handlerPath))
            {
                $handlerPath = COLBY_SITE_DIRECTORY .
                    '/colby/handlers/' .
                    $fileName;
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

        // search for handlers

        else
        {
            // check for full URL handler
            // filenames use encoded stubs (no spaces or special characters)

            $fullFilename = 'handle-' .
                implode(',', self::$encodedStubs);

            $path = COLBY_SITE_DIRECTORY .
                '/handlers/' .
                $fullFilename .
                '.php';

            if (file_exists($path))
            {
                $handlerPath = $path;
            }

            // check for partial URL handlers

            if (null === $handlerPath)
            {
            }

            // check for full URL handler in colby system

            if (null === $handlerPath)
            {
                $path = COLBY_SITE_DIRECTORY .
                    '/colby/handlers/' .
                    $fullFilename .
                    '.php';

                if (file_exists($path))
                {
                    $handlerPath = $path;
                }
            }
        }

        if ($handlerPath !== null)
        {
            // this is a valid set of stubs but the URL may not be canonical
            // calling canonicalizeRequestURI will return if the URI
            // is canonical or it will redirect if the URI isn't canonical

            self::canonicalizeRequestURI();
        }
        else
        {
            $fileName = 'handle-special-default.php';

            $handlerPath = COLBY_SITE_DIRECTORY .
                '/handlers/' .
                $fileName;

            if (!is_file($handlerPath))
            {
                $handlerPath = COLBY_SITE_DIRECTORY .
                    '/colby/handlers/' .
                    $fileName;
            }
        }

        if (!is_readable($handlerPath))
        {
            throw new RuntimeException('the URL handler: "' .
                $handlerPath .
                '" is not readable.');
        }

        include($handlerPath);
    }

    ///
    /// this function should be run only once
    /// it is run automatically when ColbyRequest is first included
    ///
    public static function initialize()
    {
        // step 1: separate url from query string
        //
        // $matches[1]: encoded requested URI
        // $matches[2]: query string (may or may not be present)

        preg_match('/^(.*?)(\?.*)?$/',
            $_SERVER['REQUEST_URI'],
            $matches);

        self::$decodedRequestURI = urldecode($matches[1]);

        if (isset($matches[2]))
        {
            self::$requestedQueryString = $matches[2];
        }
        else
        {
            self::$requestedQueryString = '';
        }

        // step 2: get stubs
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

        self::$encodedStubs = array();

        foreach (self::$decodedStubs as $decodedStub)
        {
            self::$encodedStubs[] = urlencode($decodedStub);
        }
    }

    ///
    ///
    ///
    public static function decodedRequestedURI()
    {
        return self::$decodedRequestedURI;
    }
}

ColbyRequest::initialize();
