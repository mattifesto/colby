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
            $documentRootDirectory = dirname(__DIR__);
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


require_once cbsysdir() . '/classes/Colby/Colby.php';
