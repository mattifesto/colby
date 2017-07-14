<?php

// This file contains core Colby functions that can be run without requiring any
// other dependencies. For instance, initial setup uses theses functions.

/**
 * This function is a standardized version of htmlspecialchars().
 *
 * @param string $text
 *
 * @return string (HTML)
 */
function cbhtml($text) {
    return htmlspecialchars($text, ENT_QUOTES);
}

/**
 * @param string $markdown
 *
 *      The goal is for the full CommonMark spec to be supported.
 *
 * @return string (HTML)
 */
function cbmdhtml($markdown) {
    return (new Parsedown())->text($markdown);
}

/**
 * This behaves almost exactly like `array_map` except that it passes the key
 * as well as the value to the callback function.
 *
 * @return {array}
 */
function cb_array_map_assoc(callable $callback, $array) {
    $result = [];

    foreach ($array as $key => $value) {
        $result[$key] = call_user_func($callback, $key, $value);
    }

    return $result;
}

/**
 * @param string $name
 * @param mixed $default
 * @param callable $transform
 *
 * @return mixed
 */
function cb_post_value($name, $default = null, callable $transform = null) {
    if (empty($_POST[$name])) {
        return $default;
    } else {
        $value = $_POST[$name];

        if ($transform !== null) {
            $value = call_user_func($transform, $value);
        }

        return $value;
    }
}

/**
 * @param string $name
 * @param string $default
 *
 *      This parameter doesn't have to be a string, but since the value will be
 *      a string if it is set in the query string, the default should usually be
 *      a string also.
 *
 * @return string
 */
function cb_query_string_value($name, $default = '', callable $transform = null) {
    if (empty($_GET[$name])) {
        return $default;
    } else {
        $value = $_GET[$name];

        if ($transform !== null) {
            $value = call_user_func($transform, $value);
        }

        return $value;
    }
}
