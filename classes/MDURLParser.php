<?php

class MDURLParser
{
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

        $url = urldecode($matches[1]);

        if (isset($matches[2]))
        {
            $queryString = $matches[2];
        }
        else
        {
            $queryString = '';
        }

        // step 2: get stubs

        $this->stubs = preg_split('/[\/\s]+/',
            $url,
            null,
            PREG_SPLIT_NO_EMPTY);

        // step 3: redirect to canonicalized URL if necessary

        $canonicalURL = '/';

        if (count($this->stubs) === 1 && 'index.php' === $this->stubs[0])
        {
            // canonical URL is still '/'
        }
        else if (count($this->stubs) > 0)
        {
            $canonicalURL = '/' . implode('/', $this->stubs) . '/';
        }

        if ($url !== $canonicalURL)
        {
            $redirectURI = $canonicalURL . $queryString;

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
