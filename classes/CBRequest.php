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
     * @return  {string}
     *  The string returned will be properly URL encoded but will not have HTML
     *  entities encoded.
     *
     *  Example:
     *
     *      ?page=2&name=Bob+Jones
     */
    public static function canonicalQueryString(array $pairs = []) {
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
}
