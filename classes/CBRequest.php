<?php

final class CBRequest {

    /**
     * @return bool
     *
     *      Returns true if this was an Ajax request; otherwise false.
     */
    static function callAjaxFunction() {
        $modelAsJSON = cb_post_value('ajax');

        if (empty($modelAsJSON)) {
            return false;
        }

        $response = new CBAjaxResponse();
        $model = json_decode($modelAsJSON);
        $className = CBModel::value($model, 'functionClassName');
        $functionName = CBModel::value($model, 'functionName');
        $args = CBModel::valueAsObject($model, 'args');

        $function = "{$className}::{$functionName}Ajax";
        $getGroupFunction = "{$className}::{$functionName}Group";

        if (is_callable($function) && is_callable($getGroupFunction)) {
            $group = call_user_func($getGroupFunction);

            if (ColbyUser::currentUserIsMemberOfGroup($group)) {
                $response->cancel();
                return call_user_func($function, $args);
            } else if (ColbyUser::currentUserId() === null) {
                $response->message          = "The requested Ajax function cannot be called because you are not currently logged in, possibly because your session has timed out. Reloading the current page will usually remedy this.";
                $response->userMustLogIn    = true;
            } else {
                $response->message          = "You do not have permission to call a requested Ajax function.";
                $response->userMustLogIn    = false;
            }
        } else {
            CBLog::addMessage(__METHOD__, 5, "A request was made to call the ajax function {$className}::{$functionName} which is not implemented.");
            $response->message = 'You do not have permission to call a requested Ajax function.';
        }

        $response->send();
    }


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
     * @return  {string}
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
     * @return [{string}]
     */
    static function decodedPathToDecodedStubs($decodedPath) {
        return preg_split('/[\/]+/', $decodedPath, null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @return {string}
     */
    static function decodedPathToCanonicalEncodedPath($decodedPath) {
        $stubs = CBRequest::decodedPathToDecodedStubs($decodedPath);
        $stubs = array_map('rawurlencode', $stubs);
        $path = implode('/', $stubs);
        return "/{$path}/";
    }

    /**
     * @return {string}
     */
    static function requestURIToOriginalEncodedPath($requestURI = null) {
        $requestURI = ($requestURI !== null) ? $requestURI : $_SERVER['REQUEST_URI'];

        preg_match('/^(.*?)(\?.*)?$/', $requestURI, $matches);

        return $matches[1];
    }

    /**
     * @return {string}
     */
    static function requestURIToOriginalEncodedQueryString($requestURI = null) {
        $requestURI = ($requestURI !== null) ? $requestURI : $_SERVER['REQUEST_URI'];

        preg_match('/^(.*?)(\?.*)?$/', $requestURI, $matches);

        return isset($matches[2]) ? $matches[2] : '';
    }
}
