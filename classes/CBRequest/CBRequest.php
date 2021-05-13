<?php

final class CBRequest {

    /**
     * The original intent of this function is to allow a page preview to work
     * with query variables. If it is eventually enhanced to be used for some
     * additional purpose, document it in the comments.
     *
     * @param   {array}     $pairs
     *
     *      [
     *          ['key1', 'value1'],
     *          ['key2', 'value2'],
     *          ...
     *      ]
     *
     * @return  string
     *  The string returned will be properly URL encoded but will not have HTML
     *  entities encoded.
     *
     *  Example:
     *
     *      ?page=2&name=Bob+Jones
     */
    static function canonicalQueryString(array $pairs = []) {
        if (isset($_GET['iteration'])) {
            array_unshift($pairs, ['iteration', $_GET['iteration']]);
        }

        if (isset($_GET['ID'])) {
            array_unshift($pairs, ['ID', $_GET['ID']]);
        }

        if (empty($pairs)) {
            return;
        } else {
            $pairs = array_map(function($pair) {
                $name   = urlencode($pair[0]);
                $value  = urlencode($pair[1]);
                return "{$name}={$value}";
            }, $pairs);

            return '?' . implode('&', $pairs);
        }
    }

    /**
     * PREG_SPLIT_NO_EMPTY
     * This will prevent preg_split from returning empty stubs from before the
     * first and after the last slash.
     *
     * Repeated slashes are treated as one because of the '+' in '[\/]+'. If
     * there are repeated slashes the URL is not canonical and will be
     * rewritten.
     *
     * @return [string]
     */
    static function decodedPathToDecodedStubs($decodedPath) {
        return preg_split('/[\/]+/', $decodedPath, null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @return string
     */
    static function decodedPathToCanonicalEncodedPath($decodedPath) {
        $stubs = CBRequest::decodedPathToDecodedStubs($decodedPath);
        $stubs = array_map('rawurlencode', $stubs);
        $path = implode('/', $stubs);
        return "/{$path}/";
    }
    /* decodedPathToCanonicalEncodedPath() */



    /**
     * @return void
     */
    static function
    redirectSecondaryDomainsToPrimaryDomain(
    ): void {
        $requestDomain = CBRequest::requestDomain();
        $primaryDomain = CBConfiguration::primaryDomain();

        if ($requestDomain !== $primaryDomain) {
            $primaryDomainURL = (
                'https://' .
                $primaryDomain .
                $_SERVER['REQUEST_URI']
            );

            header(
                'Location: ' . $primaryDomainURL,
                true,
                301
            );

            exit;
        }
    }
    /* redirectSecondaryDomainsToPrimaryDomain() */



    /**
     * Returns the domain used by the current request which may be the primary
     * domain or a secondary domain.
     *
     * @NOTE
     *
     *      Use CBConfiguration::primaryDomain() to get the primary domain which
     *      may or may not match the request domain.
     *
     * @return string
     */
    static function
    requestDomain (
    ): string {
        return $_SERVER['SERVER_NAME'];
    }
    /* requestDomain() */



    /**
     * @return string
     *
     *      The function returns peformated text like the following:
     *
     *      host: example.com
     *       URI: /
     *
     *      Ajax
     *      className: CBModels
     *       function: fetchSpec
     *      arguments: {
     *          "ID": "81a1dff50b2673d003f6f65ae2e7a99bcad2a1de"
     *      }
     */
    static function requestInformation() {
        $message = '';

        if (isset($_SERVER['SERVER_NAME'])) {
            $message .= "host: " . $_SERVER['SERVER_NAME'] . "\n";
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
            $message .= " URI: $parts[0]\n";
            if (isset($parts[1])) {
                $message .= "Query String: $parts[1]\n";
            }
            $message .= "\n";
        }

        if (isset($_POST['ajax'])) {
            $ajaxModelAsJSON = cb_post_value('ajax');
            $ajaxModel = json_decode($ajaxModelAsJSON);

            $ajaxFunctionClassName = CBModel::value(
                $ajaxModel,
                'functionClassName',
                '(unset)'
            );

            $ajaxFunctionName = CBModel::value(
                $ajaxModel,
                'functionName',
                '(unset)'
            );

            $message .= (
                "Ajax\n" .
                "className: {$ajaxFunctionClassName}\n" .
                " function: {$ajaxFunctionName}\n"
            );

            $args = CBModel::valueAsObject(
                $ajaxModel,
                'args'
            );

            if (!empty($args)) {
                $message .= (
                    "arguments: " .
                    json_encode(
                        $args,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    ) .
                    "\n"
                );
            }
        }

        return $message;
    }
    /* requestInformation() */



    /**
     * @param string|null $requestURI
     *
     * @return string
     */
    static function requestURIToOriginalEncodedPath(
        $requestURI = null
    ) {
        $requestURI = (
            ($requestURI !== null) ?
            $requestURI :
            $_SERVER['REQUEST_URI']
        );

        preg_match(
            '/^(.*?)(\?.*)?$/',
            $requestURI,
            $matches
        );

        return $matches[1];
    }
    /* requestURIToOriginalEncodedPath() */



    /**
     * @param string|null $requestURI
     *
     * @return string
     */
    static function requestURIToOriginalEncodedQueryString(
        $requestURI = null
    ) {
        $requestURI = (
            ($requestURI !== null) ?
            $requestURI :
            $_SERVER['REQUEST_URI']
        );

        preg_match(
            '/^(.*?)(\?.*)?$/',
            $requestURI,
            $matches
        );

        return (
            isset($matches[2]) ?
            $matches[2] :
            ''
        );
    }
    /* requestURIToOriginalEncodedQueryString() */



    /**
     * @return void
     */
    static function setNoCacheHeaders(): void {
        header(
            'Cache-Control: private, no-cache, no-store, must-revalidate'
        );

        header(
            'Expires: Sat, 01 Jan 2000 00:00:00 GMT'
        );

        header(
            'Pragma: no-cache'
        );
    }
    /* setNoCacheHeaders() */

}
