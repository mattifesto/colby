<?php

Colby::initialize();

/**
 *
 */
final class Colby {

    /**
     * These constants are used as parameters to the `find` methods.
     */

    const returnAbsoluteFilename    = 0;
    const returnURL                 = 1;

    /**
     * These constants are used by the `encrypt` and `decrypt` methods.
     */

    const countOfInitializationVectorBytes  = 16;
    const encryptionMethod                  = 'aes-256-cbc';
    const encryptionOptions                 = 0;

    // mysqli
    // This holds the mysqli object if the request needs database access.

    private static $mysqli = null;

    // libraryDirectories
    // A list of root relative directories to be search when looking for
    // snippets, handlers, or document related files.

    public static $libraryDirectories = [];

    /**
     * @return bool
     */
    static function autoload($className) {
        foreach (Colby::$libraryDirectories as $directory) {
            $directory = empty($directory) ? '' : "/{$directory}";
            $filepath = cbsitedir() . "{$directory}/classes/{$className}.php";

            if (is_file($filepath)) {
                break;
            }

            $filepath = cbsitedir() . "{$directory}/classes/{$className}/{$className}.php";

            if (is_file($filepath)) {
                break;
            }

            $filepath = null;
        }

        if (empty($filepath)) {
            return false;
        } else {
            include_once $filepath;
            return true;
        }
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v357.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v453.js', cbsysurl()),
        ];
    }

    /**
     * @NOTE 2018.09.06
     *
     *      The function imageToURL() in Colby.js has been deprecated and moved
     *      to toURL() in CBImage.js. Once all callers have been migrated the
     *      CBImage required class name can be removed.
     *
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBImage',
        ];
    }

    /**
     * If the site is marked as being debugged this function will send a message
     * to the PHP error log. If the site isn't being debugged it will do
     * nothing.
     *
     * This function works well for deprecated function messages and other
     * messages that a developer would want to know about but may not be able
     * take action on immediately.
     *
     * This function should not be used for messages that require immediate
     * action. Those issues should be resolved right away.
     *
     * @return void
     */
    static function debugLog($message) {
        if (self::siteIsBeingDebugged()) {
            error_log("Debug Log: {$message}");
        }
    }

    /**
     * @param string $cipherDataString
     *
     *      This must be a string that was returned by the `encrypt` function.
     *
     * @return mixed|null
     *
     *      The data type of the returned data will match the data type that was
     *      passed into the `encrypt` function. Will return null if unable to
     *      decrypt the cipher data string.
     */
    static function decrypt($cipherDataString) {
        $cipherData = unserialize($cipherDataString);

        if ($cipherData->version != 1) {
            throw new RuntimeException("Unknown Colby cipher data version: {$cipherData->version}.");
        }

        $serializedData = openssl_decrypt($cipherData->ciphertext,
                                          Colby::encryptionMethod,
                                          CBEncryptionPassword,
                                          Colby::encryptionOptions,
                                          $cipherData->initializationVector);

        if (false === $serializedData) {
            return null;
        }

        return unserialize($serializedData);
    }

    /**
     * Encryption is incredibly complex. This function and the `decrypt`
     * function appear simple but it took days of research before they were
     * completed. That research is not apparent here, so here are some notes:
     *
     *      -   AES-256 is the encryption method used and is currently the best
     *          choice.
     *      -   CBC is the encryption mode used and is currently either the
     *          best choice or tied for the best choice.
     *      -   The fact that the initialization vector is publicly visible is
     *          not a security concern in any way.
     *
     * @param mixed $data
     *
     *  The `$data` parameter can be of any data type. This function will
     *  serialize it to a string before encrypting it. The `decrypt` function
     *  will unserialize it so the same data type is returned and the whole
     *  process is transparent to the caller.
     *
     * @return string
     *
     *  The returned string is actually a serialized object. It's fine if the
     *  caller wants to take advantage of this fact, however it would probably
     *  be of little use to do so.
     */
    static function encrypt($data) {
        $serializedData = serialize($data);
        $cipherData = new stdClass();

        /**
         * The cipher data has a version just in case we need to update the
         * encryption parameters. This way we will be able to detect and
         * decrypt data encrypted before the changes.
         */

        $cipherData->version = 1;
        $cipherData->initializationVector = openssl_random_pseudo_bytes(self::countOfInitializationVectorBytes);
        $cipherData->ciphertext = openssl_encrypt($serializedData,
                                                  self::encryptionMethod,
                                                  CBEncryptionPassword,
                                                  self::encryptionOptions,
                                                  $cipherData->initializationVector);

        return serialize($cipherData);
    }

    /**
     * @deprecated This function returns more that just the stack trace and
     * should be avoided. Callers should be refactored.
     *
     * @return string
     */
    static function exceptionStackTrace($exception) {
        ob_start();

        echo CBRequest::requestInformation();
        echo "\n\n";
        echo CBConvert::throwableToStackTrace($exception);

        return ob_get_clean();
    }

    /**
     * This function searches the website, the Colby system, and the libraries
     * for a file, usually in that order. The behavior of this function is what
     * allows the website to override the behavior of the Colby system and the
     * libraries.
     *
     * @param string path
     *
     *  The relative path of the file to be found, for example:
     *
     *  "handlers/handle,view-cart.php"
     *  "setup/update-database.php"
     *
     * @return string | null
     */
    static function findFile($path, $returnFormat = Colby::returnAbsoluteFilename) {
        foreach (self::$libraryDirectories as $libraryDirectory) {
            if ($libraryDirectory) {
                $intraSiteFilename = "{$libraryDirectory}/{$path}";
            } else {
                $intraSiteFilename = $path;
            }

            $absoluteFilename = COLBY_SITE_DIRECTORY . "/{$intraSiteFilename}";

            if (is_file($absoluteFilename)) {
                switch ($returnFormat) {
                    case Colby::returnAbsoluteFilename:
                        return $absoluteFilename;
                    case Colby::returnURL:
                        return CBSitePreferences::siteURL() . "/{$intraSiteFilename}";
                    default:
                        throw new InvalidArgumentException('returnFormat');
                }
            }
        }

        return null;
    }

    /**
     * @deprecated use findFile
     *
     * @return string | null
     */
    static function findHandler($filename, $returnFormat = Colby::returnAbsoluteFilename) {
        $path = "handlers/{$filename}";
        return self::findFile($path, $returnFormat);
    }

    /**
     * @deprecated use findFile
     *
     * @return string | null
     */
    static function findSnippet($filename) {
        $path = "snippets/{$filename}";
        return self::findFile($path);
    }

    /**
     * @deprecated use Colby::flexpath()
     *
     * @param string $flexdir
     * @param string $className
     *
     * @return string
     */
    static function flexnameForCSSForClass($flexdir, $className) {
        return Colby::flexpath($className, 'css', $flexdir);
    }

    /**
     * @deprecated use Colby::flexpath()
     *
     * @param string $flexdir
     * @param string $className
     *
     * @return string
     */
    static function flexnameForJavaScriptForClass($flexdir, $className) {
        return Colby::flexpath($className, 'js', $flexdir);
    }

    /**
     * This function builds a flexpath from a class name. It is mosly used to
     * construct flexpaths for css and js files associated with a class.
     *
     * It mirrors CBDataStore::flexpath() in the way it chooses and orders its
     * paramaters from most important to optional.
     *
     * @param string $className
     * @param string $extension
     * @param string? $flexdir
     *
     * @return string
     */
    static function flexpath($className, $extension, $flexdir = null) {
        $flexpath = "classes/{$className}/{$className}.{$extension}";

        if (empty($flexdir)) {
            return $flexpath;
        } else {
            return "{$flexdir}/{$flexpath}";
        }
    }

    /**
     * Find files in all libraries.
     *
     * @param string $pattern
     *
     *      Example:
     *      (extra spaces are to avoid ending comment)
     *
     *      $filepaths = Colby::globFiles('classes / * / *.mmk');
     *
     *      This will find all .mmk file inside a class directory.
     *
     * @return [string]
     */
    static function globFiles($pattern) {
        $filenames = array();

        foreach (Colby::$libraryDirectories as $libraryDirectory) {
            if ($libraryDirectory) {
                $intraSitePattern = "{$libraryDirectory}/{$pattern}";
            } else {
                $intraSitePattern = $pattern;
            }

            $libraryFilenames = glob(cbsitedir() . "/{$intraSitePattern}");
            $filenames = array_merge($filenames, $libraryFilenames);
        }

        return $filenames;
    }

    /**
     * @return void
     */
    static function handleError($errno, $errstr, $errfile, $errline) {
        $severity = 2;
        throw new ErrorException($errstr, $errno, $severity, $errfile, $errline);
    }

    /**
     * @deprecated use CBErrorHandler::handle()
     */
    static function handleException(Throwable $throwable) {
        CBErrorHandler::handle($throwable);
    }

    /**
     * This function exists to catch and report fatal errors which are not
     * sent through the traditional PHP error handler.
     *
     * Errors that are handled by the traditional PHP error handler will not be
     * returned by the `error_get_last` function below so we won't accidentally
     * over-report non-fatal errors.
     *
     * @return null
     */
    static function handleShutdown() {
        $error = error_get_last();

        if ($error) {
            $severity   = 1;
            $message    = 'Fatal Error: ' . $error['message'];
            $number     = $error['type'];
            $filename   = $error['file'];
            $line       = $error['line'];

            $exception  = new ErrorException($message, $number, $severity, $filename, $line);

            CBErrorHandler::report($exception, $severity);
        }
    }

    /**
     * To use Colby you must include init.php which includes this file which
     * runs this function. The contents of this function may be entirely or
     * partially moved to init.php over time. The init.php file is how Colby is
     * booted.
     *
     * This function should be well documented with comments and even a new
     * developer should be able to read it to understand how the system gets
     * initialized.
     *
     * Before this function runs, the following are available:
     *
     *      CBSiteDirectory (<CBSystemDirectory>/init.php)
     *      CBSiteVersionNumber (<CBSiteDirectory>/version.php)
     *      CBSystemDirectory (<CBSystemDirectory>/init.php)
     *      CBSystemVersionNumber (<CBSystemDirectory>/version.php)
     *
     *      <CBSystemDirectory>/function.php has already been included by
     *      <CBSystemDirectory>/init.php
     */
    static function initialize() {

        /**
         * Library directories are relative paths from the site directory. So
         * the site library directory is simply an empty string.
         *
         * Add the site and Colby system library directories as the first two
         * library directories. These library directories are required for
         * error handling to function properly.
         *
         * Library directories at the lowest index in the array are checked
         * first and therefore have the highest priority. When searching for
         * library files, the site library will have the highest priority and
         * the Colby library will have the next highest priority. When the
         * "site-configuration.php" file is included later in this method,
         * any additional libraries will have still lower priority with the
         * libraries loaded earlier having greater priority than the libraries
         * loaded later.
         */

        Colby::$libraryDirectories[] = '';
        Colby::$libraryDirectories[] = 'colby';

        /**
         * Set up autoloading. Autoloading is used by error handling. Colby used
         * to try to make sure error handling used no other files, but when
         * things are working well, more advanced functionality is desired to
         * log a report errors to system administrators.
         *
         * After this call, autoloadng will be enabled for the site library and
         * the Colby library. More libraries will be added when
         * site-configuration.php is included below.
         *
         * @NOTE This comment replaces earlier comments about how this must be
         * the last autoloader because it can throw an exception. However, after
         * studying Colby::autoload() this doesn't appear to be explicitly true.
         * The earlier comment was written poorly. If you find there are
         * limitations in this area, document them clearly and explicity here.
         */

        spl_autoload_register('Colby::autoload');

        /**
         * Once the first library directories are configured and autoloading
         * is started, we can set up error handling.
         */

        set_error_handler('Colby::handleError');
        set_exception_handler('CBErrorHandler::handle');
        register_shutdown_function('Colby::handleShutdown');

        /**
         * Once error handling is enabled, the local configuration file can be
         * included. This file is not checked in and therefore is not shared
         * between different versions of the site. If it has a problem, error
         * handling must be enabled to let the site administrator know about it.
         *
         * Every once in a while it's a good idea to insert a syntax error into
         * colby-configuration.php and make sure that an error is reported when
         * trying to access a page.
         *
         *      2017.08.17 When a syntax error was placed and a page was loaded
         *      the error was correctly reported on the page. The error was also
         *      correctly reported to Slack. Additionally an inner exception
         *      during CBLog::addMessage() was reported because the syntax error
         *      was placed before the database constants are set causing the
         *      function to fail writing to the database. This is fine.
         *
         * This file defines CBSiteURL.
         */

        include_once cbsitedir() . '/colby-configuration.php';

        /**
         * Now that the CBSiteURL constant has been defined, the CBSystemURL
         * constant can also be defined.
         */

        define('CBSystemURL', cbsiteurl() . "/colby");
        define('COLBY_SYSTEM_URL', cbsysurl()); // @deprecated

        /**
         * Include the site configuration file. Unlike 'colby-configuration.php'
         * which contains instance specific settings, 'site-configuration.php'
         * is checked in and shared by all instances of the site such as
         * development, test, and production instances. The code in this file
         * should perform the following tasks:
         *
         *      1. Set shared constants used by all instances of the site.
         *      2. Call Colby::loadLibrary() for any additional libraries that
         *         are used by the site.
         *
         * After these tasks are performed autoloading will be fully functional.
         */

        include_once cbsitedir() . '/site-configuration.php';

        /**
         * 2014.08.26
         *  This check was added because if magic quotes are enabled bugs that
         *  are difficult to investigate will appear. Once PHP versions less
         *  than 5.4 are unacceptable, this code can be removed.
         */

        if (get_magic_quotes_runtime() || get_magic_quotes_gpc()) {
            $mqr = get_magic_quotes_runtime();
            $mqr = var_export($mqr, true);
            $mqg = get_magic_quotes_gpc();
            $mqg = var_export($mqg, true);

            throw new RuntimeException("Magic quotes are enabled on this server: magic_quotes_runtime={$mqr}, magic_quotes_gpc={$mqg}. Add the line 'php_flag magic_quotes_gpc off' to the `.htaccess` file.");
        }
    }

    /**
     * @param string $libraryPath
     *
     *      The path to the library directory from the site directory with no
     *      beginning or ending slashes.
     *
     *      Example: "mylibrary" or "libraries/mylibrary"
     *
     * @return void
     */
    static function loadLibrary($libraryPath): void {
        Colby::$libraryDirectories[] = $libraryPath;

        $libraryDirectory = cbsitedir() . "/{$libraryPath}";

        if (file_exists($filename = "{$libraryDirectory}/version.php")) {
            include_once $filename;
        }

        if (file_exists($filename = "{$libraryDirectory}/library-configuration.php")) {
            include_once $filename;
        }
    }

    /**
     * @return mysqli
     */
    static function mysqli() {
        if (null === Colby::$mysqli) {
            $mysqli = new mysqli(
                CBSitePreferences::mysqlHost(),
                CBSitePreferences::mysqlUser(),
                CBSitePreferences::mysqlPassword(),
                CBSitePreferences::mysqlDatabase()
            );

            if ($mysqli->connect_error) {
                throw new RuntimeException($mysqli->connect_error);
            }

            // The default MySQL character set is "latin1" but the tables use "utf8mb4"

            if (!$mysqli->set_charset('utf8mb4')) {
                throw new RuntimeException(
                    'Unable to set mysqli character set to "utf8mb4".');
            }

            Colby::$mysqli = $mysqli;
        }

        return Colby::$mysqli;
    }

    /**
     * This function is used to run multiple SQL statements with a single request
     * which will help performance. The function does not allow the caller
     * to get any results so it may not be useful for queries that return
     * results. To get results use the `query` function or use the `mysqli`
     * function to the get the `mysqli` object to use it directly.
     *
     * If there is an error in any of the SQL statements an exception will be
     * thrown.
     *
     * @return void
     */
    static function queries($sql) {
        $mysqli = Colby::mysqli();
        $indexOfTheSQLStatementWithAnError = 0;
        $theFirstSQLStatementWasSuccessful = $mysqli->multi_query($sql);

        if ($theFirstSQLStatementWasSuccessful) {

            /**
             * The following code just iterates through any result sets
             * without retrieving them. There would be result sets if the user
             * mistakenly included queries, such as SELECT queries, that return
             * results. If we don't at least loop through each of the result
             * sets, it will block future queries. Basically we need to flush
             * the result queue.
             *
             * The index is used to track which of the SQL statements contained
             * the error, if an error occurred. MySQL stops processing as soon
             * as it finds a statement with an error, so the last result will
             * always be the statement with the error.
             */

            while ($mysqli->more_results() && $mysqli->next_result()) {
                $indexOfTheSQLStatementWithAnError++;
            }
        }

        if ($mysqli->error) {
            throw new RuntimeException("Index of the SQL statement with an error: {$indexOfTheSQLStatementWithAnError}\n\nMySQL error: {$mysqli->error}");
        }
    }

    /**
     * This function is used to run a single query and check for errors.
     *
     * @param string $SQL
     * @param boolean $retryOnDeadlock
     *
     * @return mysqli_result | boolean
     */
    static function query($SQL, $retryOnDeadlock = false) {
        $mysqli = Colby::mysqli();
        $countOfDeadlocks = 0;
        $maxDeadlocks = 5;

        while (true) {
            $result = $mysqli->query($SQL);

            if ($mysqli->errno === 1213 && $retryOnDeadlock && $countOfDeadlocks < $maxDeadlocks) {
                $countOfDeadlocks += 1;
                continue;
            }

            if ($mysqli->error) {
                throw new RuntimeException("MySQL error: \"{$mysqli->error}\", MySQL error number: {$mysqli->errno}\n\n{$SQL}");
            }

            break;
        }

        return $result;
    }

    /**
     * @deprecated use CBHex160::random()
     *
     * @return {hex160}
     */
    static function random160() {
        return CBHex160::random();
    }

    /**
     * @deprecated use CBErrorHandler::report()
     *
     * @return null
     */
    static function reportException(Throwable $throwable, $severity = 3) {
        CBErrorHandler::report($throwable);
    }

    /**
     * @deprecated use `CBSitePreferences::debug()` instead
     *
     * @return {bool}
     */
    static function siteIsBeingDebugged() {
        return CBSitePreferences::debug();
    }

    /**
     * @deprecated use URLForJavaScriptForClass
     *
     * @param string $className
     *
     * @return string
     */
    static function URLForJavaScriptForSiteClass($className) {
        return CBSitePreferences::siteURL() . "/classes/{$className}/{$className}.js";
    }
}


/**
 * NOTE: 2017.03.19
 *
 *      Any code wanting the site URL should call this function to get it.
 *      Most of this time this function will return the value of the
 *      CBSiteURL constant. However, this constant should never be used
 *      directly. The implementation may change in the future.
 *
 *      A constant is currently used because if the user can edit this
 *      property and enters it incorrectly the website will stop working
 *      with very little recourse because the editor will no longer reload.
 *
 *      As other options become more reliable, or other constants provide
 *      more flexibility, this function's implementation will change.
 *
 * @return string
 *      Returns the site URL with no trailing slash.
 */
function cbsiteurl() {
    if (defined(CBSiteURL)) {
        return CBSiteURL;
    } else if (defined('COLBY_SITE_URL')) { // @deprecated
        return COLBY_SITE_URL;
    } else {
        return (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];
    }
}


/**
 * @return string
 */
function cbsysurl() {
    return CBSystemURL;
}
