<?php

class MDURLParser
{
    private $requestedURL;
    private $requestedQueryString;

    private $stubs;

    /// <summary>
    ///
    /// </summary>
    public function __construct()
    {
        // step 1: separate url from query string

        // 1: url
        // 2: query string (optional)

        preg_match('/^(.*?)(\?.*)?$/',
            $_SERVER['REQUEST_URI'],
            $matches);

        $this->requestedURL = urldecode($matches[1]);

        if (isset($matches[2]))
        {
            $this->requestedQueryString = $matches[2];
        }
        else
        {
            $this->requestedQueryString = '';
        }

        // step 2: get stubs
        // preg_split will return an empty array if there aren't any stubs

        $this->stubs = preg_split('/[\/\s]+/',
            $this->requestedURL,
            null,
            PREG_SPLIT_NO_EMPTY);
    }

    /// <summary>
    ///
    /// </summary>
    public function canonicalizeURL()
    {
        $canonicalURL = '/';

        if (count($this->stubs) === 1 && 'index.php' === $this->stubs[0])
        {
            // canonical URL is still '/'
        }
        else if (count($this->stubs) > 0)
        {
            $canonicalURL = '/' . implode('/', $this->stubs) . '/';
        }

        if ($this->requestedURL !== $canonicalURL)
        {
            $redirectURI = $canonicalURL . $this->requestedQueryString;

            header('Location: ' . $redirectURI, true, 301);
            exit;
        }
    }

    /// <summary>
    ///
    /// </summary>
    public function stubs()
    {
        return $this->stubs;
    }
}
