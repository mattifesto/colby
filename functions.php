<?php

/**
 * This file contains core Colby functions that can be run without requiring any
 * other dependencies. For instance, initial setup uses theses functions.
 */



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
 * @deprecated 2021_06_04
 *
 *      Use cb_document_root_directory().
 */
function
cbsitedir(
): string {
    return cb_document_root_directory();
}
/* cbsitedir() */



/**
 * @return string
 */
function
cbsysdir(
) {
    return __DIR__;
}
/* cbsysdir() */



/**
 * @param callable $callback
 * @param array $array
 *
 * @return bool
 *
 *      Returns true if the callback returns a truthy value for any of the array
 *      items.
 */
function cb_array_any(callable $callback, array $array) {
    foreach ($array as $item) {
        if (call_user_func($callback, $item)) {
            return true;
        }
    }

    return false;
}

/**
 * This function is a PHP version of Array.prototype.find().
 *
 * @param array $array
 * @param callable $callback
 *
 * @return mixed|null
 */
function cb_array_find(array $array, callable $callback) {
    $foundArrayElement = null;

    foreach ($array as $index => $element) {
        $result = call_user_func($callback, $element, $index, $array);

        if ($result) {
            $foundArrayElement = $element;
            break;
        }
    }

    return $foundArrayElement;
}

/**
 * This behaves almost exactly like `array_map` except that it passes the key
 * as well as the value to the callback function.
 *
 * @return array
 */
function cb_array_map_assoc(callable $callback, array $array) {
    $result = [];

    foreach ($array as $key => $value) {
        $result[$key] = call_user_func($callback, $key, $value);
    }

    return $result;
}



/**
 * @NOTE 2021_01_24
 *
 *      This function used to return realpath($_SERVER['DOCUMENT_ROOT']), but
 *      the value of DOCUMENT_ROOT when loaded by terminal does not necessarily
 *      have the same value as it does when loaded by a web server. Currently,
 *      Colby is always contained in a folder named "colby" in the site
 *      directory so returning the parent directory of the directory containing
 *      this file will be the correct value in all cases.
 *
 *      When Colby moves we may need a different approach.
 *
 * @return string
 */
function
cb_document_root_directory(
): string {
    static $documentRootDirectory = null;

    if ($documentRootDirectory === null) {
        $testDocumentRootDirectory = getenv(
            'CB_TEST_DOCUMENT_ROOT_DIRECTORY'
        );

        if ($testDocumentRootDirectory === false) {
            $documentRootDirectory = dirname(
                __DIR__
            );
        } else {
            $documentRootDirectory = $testDocumentRootDirectory;
        }
    }

    return $documentRootDirectory;
}
/* cb_document_root_directory() */



/**
 * @return string|null
 *
 *      Newer Colby projects have a project directory that contains a
 *      document_root directory and also contains a logs directory. Older
 *      projects will not have this and those projects will return null from
 *      this function.
 */
function
cb_logs_directory(
): ?string {
    static $logsDirectory = false;

    if ($logsDirectory === false) {
        $projectDirectory = cb_project_directory();

        if ($projectDirectory === null) {
            $logsDirectory = null;
        } else {
            $logsDirectory = "{$projectDirectory}/logs";
        }
    }

    return $logsDirectory;
}
/* cb_logs_directory() */



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
 * @return string|null
 *
 *      Newer Colby projects have a project directory that contains a
 *      document_root directory. Older projects will not have this and those
 *      projects will return null from this function.
 */
function
cb_project_directory(
): ?string {
    static $projectDirectory = false;

    if ($projectDirectory === false) {
        $projectDirectory = dirname(
            cbsitedir()
        );

        if (
            !is_dir(
                "{$projectDirectory}/document_root"
            )
        ) {
            $projectDirectory = null;
        }
    }

    return $projectDirectory;
}
/* cb_project_directory() */



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
function cb_query_string_value(
    string $name,
    string $default = '',
    callable $transform = null
): string {
    if (empty($_GET[$name])) {
        return $default;
    } else {
        $value = $_GET[$name];

        if ($transform !== null) {
            $value = call_user_func(
                $transform,
                $value
            );
        }

        return $value;
    }
}
/* cb_query_string_value() */
