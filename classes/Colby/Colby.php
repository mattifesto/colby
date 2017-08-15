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

    public static $libraryDirectories = array();

    /**
     * @return bool
     */
    public static function autoload($className) {
        foreach (self::$libraryDirectories as $directory) {
            $directory = empty($directory) ? '' : "/{$directory}";
            $filepath = CBSiteDirectory . "{$directory}/classes/{$className}.php";

            if (is_file($filepath)) {
                break;
            }

            $filepath = CBSiteDirectory . "{$directory}/classes/{$className}/{$className}.php";

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
    public static function debugLog($message) {
        if (self::siteIsBeingDebugged()) {
            error_log("Debug Log: {$message}");
        }
    }

    /**
     * @param string $cipherDataString
     *
     *  This must be a string that was returned by the `encrypt` function.
     *
     * @return mixed
     *
     *  The data type of the returned data will match the data type that was
     *  passed into the `encrypt` function.
     */
    public static function decrypt($cipherDataString) {
        $cipherData = unserialize($cipherDataString);

        if ($cipherData->version != 1) {
            throw new RuntimeException("Unknown Colby cipher data version: {$cipherData->version}.");
        }

        $serializedData = openssl_decrypt($cipherData->ciphertext,
                                          self::encryptionMethod,
                                          CBEncryptionPassword,
                                          self::encryptionOptions,
                                          $cipherData->initializationVector);

        if (false === $serializedData) {
            throw new RuntimeException("Unable to decrypt the ciphertext: {$cipherData->ciphertext}");
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
    public static function encrypt($data) {
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
    public static function exceptionStackTrace($exception) {
        ob_start();

        include(CBSystemDirectory . '/snippets/exception-stack-trace.php');

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
    public static function findFile($path, $returnFormat = Colby::returnAbsoluteFilename) {
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
    public static function findHandler($filename, $returnFormat = Colby::returnAbsoluteFilename) {
        $path = "handlers/{$filename}";
        return self::findFile($path, $returnFormat);
    }

    /**
     * @deprecated use findFile
     *
     * @return string | null
     */
    public static function findSnippet($filename) {
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
     * @param string $pattern
     *
     * @return array
     */
    public static function globFiles($pattern) {
        $filenames = array();

        foreach (self::$libraryDirectories as $libraryDirectory) {
            if ($libraryDirectory) {
                $intraSitePattern = "{$libraryDirectory}/{$pattern}";
            } else {
                $intraSitePattern = $pattern;
            }

            $libraryFilenames = glob(COLBY_SITE_DIRECTORY . "/{$intraSitePattern}");
            $filenames = array_merge($filenames, $libraryFilenames);
        }

        return $filenames;
    }

    /**
     * @return void
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        $severity = 2;
        throw new ErrorException($errstr, $errno, $severity, $errfile, $errline);
    }

    /**
     * Every handler needs to buffer its output and set its own exception
     * handler so that when an exception is thrown the handler discards its
     * own output buffer and restores the previous exception handler. After
     * that, it can either simply call this function or even rethrow the
     * exception since this is the default exception handler.
     *
     * This process is automated by the `CBHTMLOutput` class, which
     * most handlers use, although use of the class is not strictly necessary.
     *
     * This function assumes there is no output buffer active and that no
     * content has been output thus far to the global buffer. If there is,
     * it is the handler's fault and the handler should be fixed. The rule
     * is that if a process tries to do something and fails, that process
     * should clean up its own mess before handing the failure off to
     * another function.
     *
     * @return void
     */
    public static function handleException($exception, $handlerName = null) {

        /**
         * Exception handlers should never throw exceptions because if they
         * do, it's very difficult to debug. While working on major system
         * changes sometimes exceptions get thrown from inside this
         * exception handler. Since errors are converted to exceptions in
         * Colby, even though this exception handler doesn't throw
         * exceptions explicitly, exceptions can occur. To make sure there
         * is always a record of the exception, this exception handler
         * wraps all of its code in a try-catch block. If an exception
         * occurs, it uses error_log as a last resort to create a record
         * of what went wrong.
         *
         * However, this will most likely only occur while this function is
         * being actively worked on.
         */

        try {
            Colby::reportException($exception);

            $absoluteHandlerFilename = null;

            if ($handlerName) {
                $absoluteHandlerFilename = Colby::findHandler("handle-exception-{$handlerName}.php");
            }

            if (!$absoluteHandlerFilename) {
                $absoluteHandlerFilename = self::findHandler('handle-exception.php');
            }

            if (!$absoluteHandlerFilename) {

                /**
                 * Something would have to be wrong with the system
                 * configuration to get here, but if that happens we want to
                 * see the error in the error log otherwise debugging is hard.
                 */

                error_log($exception->getMessage());
            } else {
                include $absoluteHandlerFilename;
            }
        } catch (Exception $rareException) {
            error_log('Colby::handleException() RARE EXCEPTION: ' . $rareException->getMessage());
        }
    }

    /**
     * This function exists to catch and report fatal errors which are not
     * sent through the traditional PHP error handler.
     *
     * Errors that are handled by the traditional PHP error handler will not be
     * returned by the `error_get_last` function below so we won't accidentally
     * over-report non-fatal errors.
     *
     * @return void
     */
    public static function handleShutdown() {
        $error = error_get_last();

        if ($error) {
            $severity   = 1;
            $message    = 'Fatal Error: ' . $error['message'];
            $number     = $error['type'];
            $filename   = $error['file'];
            $line       = $error['line'];

            $exception  = new ErrorException($message, $number, $severity, $filename, $line);

            self::reportException($exception, $severity);
        }
    }

    /**
     * To use Colby, you must include this file. When you include this file,
     * this function is the first function that is called and basically boots
     * Colby.
     *
     * This function should be well documented with comments and even a new
     * developer should be able to read it to understand how the system gets
     * initialized.
     */
    static function initialize() {
        /**
         * Colby sites always run with all error reporting turned on.
         */

        error_reporting(E_ALL | E_STRICT);

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

        define('CBSiteDirectory', $_SERVER['DOCUMENT_ROOT']);
        define('CBPageTypeID', '89fe3a7d77424ba16c5101eeb0448c7688547ab2'); // @deprecated
        define('COLBY_SYSTEM_DIRECTORY', CBSystemDirectory); // @deprecated
        define('COLBY_SITE_DIRECTORY', $_SERVER['DOCUMENT_ROOT']); // @deprecated

        // defines CBSiteVersionNumber
        require_once CBSiteDirectory . '/version.php';

        /**
         * Library directories are relative paths from the site directory. So
         * the site library directory is simply an empty string.
         */

        $siteLibraryDirectory = '';

        /**
         * Calculate the Colby system library directory which is the relative
         * path between the site directory and the Colby system directory.
         * The Colby system library directory will almost always be "colby".
         */

        $colbySystemLibraryDirectory = str_replace(CBSiteDirectory . '/', '', CBSystemDirectory);

        /**
         * Add the site and Colby system library directories as the first two
         * library directories. These library directories are required for
         * error handling to function properly.
         *
         * Library directories at the lowest index in the array are checked
         * first and therefore have the highest priority. So when searching
         * for library files, the site will have the highest priority, the
         * Colby system will have the next highest priority. When the
         * "site-configuration.php" file is included later in this method,
         * any additional libraries will have still lower priority with the
         * libraries loaded earlier having greater priority than the libraries
         * loaded later.
         */

        self::$libraryDirectories[] = $siteLibraryDirectory;
        self::$libraryDirectories[] = $colbySystemLibraryDirectory;

        /**
         * Set up error handling.
         */

        set_error_handler('Colby::handleError');
        set_exception_handler('Colby::handleException');
        register_shutdown_function('Colby::handleShutdown');

        /**
         * Include the local configuration file. This file is not checked in
         * and therefore is not shared between different versions of the site.
         */

        include_once CBSiteDirectory . '/colby-configuration.php';

        /**
         * The `colby-configuration.php` file will indicate whether the Swift
         * Mailer email library is enabled for this site or not. We need to
         * include the required file from that library right after that because
         * the error handling code checks to see if the library should be
         * available and then assumes it is. From this point on if the library
         * is available and an error occurs, even in the remaining lines of
         * this function, an email will be sent.
         */

        if (defined('COLBY_EMAIL_LIBRARY_DIRECTORY')) {
            include_once COLBY_EMAIL_LIBRARY_DIRECTORY . '/lib/swift_required.php';
        }

        /**
         * Set up auto loading.
         *
         * After this call, autoloadng will be enabled for site classes and
         * Colby classes. It will not be enabled for optional 3rd party
         * libraries until 'site-configuration.php' is loaded below.
         *
         * This autoloading function must be the last autoloading function
         * because it will throw an exception if it is not able to successfully
         * load a class. This behavior is necessary because if no exception is
         * thrown a fatal error will occur and there's nothing Colby can do to
         * handle it and therefore the error will go unnoticed and the visitor
         * will get a blank page. Autoloaders added after this should use the
         * $prepend parameter to `spl_autoload_register` to ensure that they
         * are not the last autoloader.
         */

        spl_autoload_register('Colby::autoload');

        /**
         * Set the CBSystemURL constant.
         *
         * This ensures that autoloading for Colby is working.
         *
         * NOTE: 2017.03.19
         *
         *      This may move to CBSitePreferences::systemURL() in the future
         *      but I'm not sure about it now.
         */

        define('CBSystemURL', CBSitePreferences::siteURL() . "/{$colbySystemLibraryDirectory}");
        define('COLBY_SYSTEM_URL', CBSystemURL); // @deprecated

        /**
         * Include the site configuration file. Unlike 'colby-configuration.php'
         * which contains instance specific settings, 'site-configuration.php'
         * is checked in and shared by all instances of the site such as
         * development, test, and production instances. This code in this file
         * should perform the following tasks:
         *
         *      1. Set shared constants used by all instances of the site.
         *      2. Call Colby::loadLibrary() for any 3rd party libraries that
         *         are used by the site.
         *
         * After these tasks are performed autoloading will be fully enabled.
         */

        include_once CBSiteDirectory . '/site-configuration.php';

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
     * @return void
     */
    public static function loadLibrary($libraryDirectory) {
        $absoluteLibraryDirectory = COLBY_SITE_DIRECTORY . "/{$libraryDirectory}";

        include_once "{$absoluteLibraryDirectory}/version.php";
        include_once "{$absoluteLibraryDirectory}/library-configuration.php";

        self::$libraryDirectories[] = $libraryDirectory;
    }

    /// <summary>
    ///
    /// </summary>
    public static function mysqli() {
        if (null === self::$mysqli) {
            $mysqli = new mysqli(
                CBSitePreferences::mysqlHost(),
                CBSitePreferences::mysqlUser(),
                CBSitePreferences::mysqlPassword(),
                CBSitePreferences::mysqlDatabase()
            );

            if ($mysqli->connect_error) {
                throw new RuntimeException($mysqli->connect_error);
            }

            // The default MySQL character set is "latin1" but the tables use "utf8"

            if (!$mysqli->set_charset('utf8')) {
                throw new RuntimeException(
                    'Unable to set mysqli character set to "utf8".');
            }

            self::$mysqli = $mysqli;
        }

        return self::$mysqli;
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
    public static function queries($sql) {
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
    public static function query($SQL, $retryOnDeadlock = false) {
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
    public static function random160() {
        return CBHex160::random();
    }

    /**
     * This function is responsible for reporting an exception in various ways
     * if they are available. For instance if emails services is available and
     * the system is set up to email exceptions to an administrator, this
     * function will send that email.
     *
     * This function will not throw an exception itself. If it experiences an
     * exception it will output messages for both the original exception and
     * the exception that was thrown within to the error log.
     *
     * This function can be called form anywhere to report an exception which
     * is useful when code catches an exception it wants to report but doesn't
     * want to re-throw.
     *
     * @param Exception $exception
     * @param int $severity
     *  An RFC3164 severity code. See CBLog::addMessage().
     *
     * @return null
     */
    public static function reportException(Exception $exception, $severity = 3) {
        try {
            $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'Unknown server name';
            $model = (object)[
                'exceptionStackTrace' => Colby::exceptionStackTrace($exception),
            ];

            $exMessage = $exception->getMessage();
            $exBasename = basename($exception->getFile());
            $exLine = $exception->getLine();
            $logMessage = "\"{$exMessage}\" in {$exBasename} line {$exLine}";
            CBLog::addMessage(__METHOD__, $severity, $logMessage, $model);

            /**
             * Report the exception via email to the administrator
             */

            if (CBSitePreferences::sendEmailsForErrors() && class_exists('Swift_SmtpTransport')) {
                $transport = Swift_SmtpTransport::newInstance(COLBY_EMAIL_SMTP_SERVER,
                                                              COLBY_EMAIL_SMTP_PORT,
                                                              COLBY_EMAIL_SMTP_SECURITY);

                $transport->setUsername(COLBY_EMAIL_SMTP_USER);
                $transport->setPassword(COLBY_EMAIL_SMTP_PASSWORD);

                $mailer = Swift_Mailer::newInstance($transport);

                $truncatedMessage = CBConvert::truncate($exception->getMessage());
                $severityDescription = CBLog::severityToDescription($severity);
                $messageSubject = "{$severityDescription} | {$serverName} | {$truncatedMessage}";
                $messageFrom = array(COLBY_EMAIL_SENDER => COLBY_EMAIL_SENDER_NAME);
                $messageTo = CBSitePreferences::administratorEmails();
                $messageBodyHTML = '<pre>' . cbhtml($model->exceptionStackTrace) . '</pre>';

                $message = Swift_Message::newInstance();
                $message->setSubject($messageSubject);
                $message->setFrom($messageFrom);
                $message->setTo($messageTo);
                $message->setBody($model->exceptionStackTrace);
                $message->addPart($messageBodyHTML, 'text/html');

                $mailer->send($message);
            }
        } catch (Exception $innerException) {
            try {
                $model = (object)[
                    'exceptionStackTrace' => Colby::exceptionStackTrace($innerException),
                ];
                $innerExceptionMessage = 'Inner exception: ' . $innerException->getMessage();

                CBLog::addMessage(__METHOD__, 2, $innerExceptionMessage, $model);
            } catch (Exception $ignoredException) {
                // At this point we're three exceptions deep so we just try to
                // get an error log message written if possible.
                error_log('Source: ' . __METHOD__ . '(), Ignored exception: ' . $ignoredException->getMessage());
            }
        }
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', CBSystemURL)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', CBSystemURL)];
    }

    /**
     * @deprecated use `CBSitePreferences::debug()` instead
     *
     * @return {bool}
     */
    public static function siteIsBeingDebugged() {
        return CBSitePreferences::debug();
    }

    /**
     * @deprecated use URLForJavaScriptForClass
     *
     * @param string $className
     *
     * @return string
     */
    public static function URLForJavaScriptForSiteClass($className) {
        return CBSitePreferences::siteURL() . "/classes/{$className}/{$className}.js";
    }
}

/**
 * @return string
 */
function cbsitedir() {
    return CBSiteDirectory;
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
function cbsysdir() {
    return CBSystemDirectory;
}

/**
 * @return string
 */
function cbsysurl() {
    return CBSystemURL;
}
