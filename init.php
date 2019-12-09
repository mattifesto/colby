<?php

set_exception_handler(
    function (Throwable $throwable) {
        $message = $throwable->getMessage();
        $filename = $throwable->getFile();
        $line = $throwable->getLine();

        error_log(
            "\"{$message}\" in {$filename} line {$line}" .
            ' | error log entry made in ' .
            __FILE__
        );
    }
);



/**
 * Colby sites always run with all error reporting turned on.
 */

error_reporting(E_ALL | E_STRICT);


/**
 * These constants are set but shouldn't be used. Use the cbsitedir() and
 * cbsysdir() functions instead.
 */

define(
    'CBSiteDirectory',
    realpath($_SERVER['DOCUMENT_ROOT'])
);

define(
    'CBSystemDirectory',
    __DIR__
);

/**
 * @return string
 */
function cbsitedir() {
    return CBSiteDirectory;
}

/**
 * @return string
 */
function cbsysdir() {
    return CBSystemDirectory;
}

/* deprecated */
define(
    'COLBY_SYSTEM_DIRECTORY',
    CBSystemDirectory
);

/* deprecated */
define(
    'COLBY_SITE_DIRECTORY',
    CBSiteDirectory
);


/**
 * Includes performed before setting up error handling should use
 * `require` or `require_once` to halt execution if they aren't
 * successful.
 *
 * Includes performed after setting up error handling should use
 * `include` or `include_once` which will invoke the error handling
 * mechanism if they aren't successful.
 *
 * Only the following lines should use `require_once`.
 */

require_once cbsitedir() . '/version.php';
require_once cbsysdir() . '/version.php';
require_once cbsysdir() . '/functions.php';
require_once cbsysdir() . '/classes/Colby/Colby.php';

CBSitePreferences::initialize();
