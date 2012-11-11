<?php

// TODO: Document official handler file naming a search policy in comments
//       in this file

class ColbyRequest
{
    private static $decodedRequestURI;
    // type: string
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

    /**
     * @return string
     */
    public static function absoluteDefaultHandlerFilename()
    {
        return Colby::findHandler('handle-special-default.php');
    }

    /**
     * @return string
     */
    public static function absoluteFrontPageHandlerFilename()
    {
        return Colby::findHandler('handle-special-front-page.php');
    }

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

    ///
    /// it's not required that this function be called
    /// however most sites will call this function from index.php
    /// to handle requests in the standard way
    /// which is to search for the appropriate hanlder file and load it
    ///
    public static function handleRequest()
    {
        $countOfStubs = count(self::$decodedStubs);
        $handlerFilename = null;

        // handle front page request

        if (0 === $countOfStubs)
        {
            $handlerFilename = self::absoluteFrontPageHandlerFilename();
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

        // if we reach this code it means either no valid handler was found
        // or including the found handler returned a non-standard value

        // a valid handler will return a non-standard value (anything other than 1)
        // to communicate that a sub-stub does not exist
        // it's only the handler that is able to determine that fact

        include(self::absoluteDefaultHandlerFilename());
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
