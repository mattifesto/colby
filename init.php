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
            !is_dir("{$projectDirectory}/document_root")
        ) {
            $projectDirectory = null;
        }
    }

    return $projectDirectory;
}
/* cb_project_directory() */



/**
 * @NOTE 2021_01_24
 *
 *      This function used to return realpath($_SERVER['DOCUMENT_ROOT']), but
 *      the value of DOCUMENT_ROOT when loaded by terminal does not necessarily
 *      have that same value as it does when loaded by a web server. Colby is
 *      always contained in a folder named "colby" in the site directory so
 *      returning the parent directory of the directory containing this file
 *      will be the correct value in all cases.
 *
 * @return string
 */
function
cbsitedir(
) {
    static $cbsitedir = null;

    if ($cbsitedir === null) {
        $cbsitedir = dirname(__DIR__);
    }

    return $cbsitedir;
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
 * @deprecated 2021_01_24
 *
 *      The definition of CBSiteDirectory and COLBY_SITE_DIRECTORY should be
 *      removed in version 676.
 */

define(
    'CBSiteDirectory',
    cbsitedir()
);

define(
    'COLBY_SITE_DIRECTORY',
    cbsitedir()
);



$websiteVersionFilepath = cbsitedir() . '/version.php';

if (file_exists($websiteVersionFilepath)) {
    include_once($websiteVersionFilepath);
} else {
    define('CBSiteVersionNumber', 'setup');
}

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

require_once cbsysdir() . '/version.php';
require_once cbsysdir() . '/functions.php';
require_once cbsysdir() . '/classes/Colby/Colby.php';

CBSitePreferences::initialize();
