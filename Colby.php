<?php

Colby::initialize();

ColbyRequest::handleRequest();

class Colby
{
    /**
     * These constants are used as parameters to the 'find' methods.
     */
    const returnAbsoluteFilename    = 0;
    const returnURL                 = 1;

    // mysqli
    // This holds the mysqli object if the request needs database access.

    private static $mysqli = null;

    // libraryDirectories
    // A list of root relative directories to be search when looking for
    // snippets, handlers, or document related files.

    public static $libraryDirectories = array();

    /**
     * `uniqueHashCounter` is a number that is incremented each time a unique
     * hash is requested and also used to create the hash and helps guarantee
     * uniqueness.
     */

    private static $uniqueHashCounter = 0;

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
    public static function debugLog($message)
    {
        if (COLBY_SITE_IS_BEING_DEBUGGED)
        {
            error_log("Debug Log: {$message}");
        }
    }

    /**
     * @return string
     */
    public static function exceptionStackTrace($exception)
    {
        ob_start();

        include(COLBY_SITE_DIRECTORY .
            '/colby/snippets/exception-stack-trace.php');

        return ob_get_clean();
    }

    /**
     * @return array
     *  An array of objects containing properties for each available document
     *  group.
     */
    public static function findDocumentGroups()
    {
        $documentGroups = array();

        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            $metadataFilenames = glob(
                COLBY_SITE_DIRECTORY .
                "/{$libraryDirectory}/document-groups/*/document-group.data"
            );

            foreach ($metadataFilenames as $metadataFilename)
            {
                $documentGroups[] = unserialize(file_get_contents($metadataFilename));
            }
        }

        return $documentGroups;
    }

    /**
     * @return string | null
     */
    public static function findFileForDocumentGroup($intraGroupFilename, $documentGroupId,
                                                    $returnFormat = Colby::returnAbsoluteFilename)
    {
        $intraLibraryFilename = "document-groups/{$documentGroupId}/{$intraGroupFilename}";

        return self::findFile($intraLibraryFilename, $returnFormat);
    }

    /**
     * @return array
     *  An array of objects containing properties for each available document
     *  type.
     */
    public static function findDocumentTypes($documentGroupId)
    {
        $documentTypes = array();

        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            $metadataFilenames = glob(
                COLBY_SITE_DIRECTORY .
                "/{$libraryDirectory}" .
                "/document-groups/{$documentGroupId}" .
                '/document-types/*/' .
                'document-type.data');

            foreach ($metadataFilenames as $metadataFilename)
            {
                $documentTypes[] = unserialize(file_get_contents($metadataFilename));
            }
        }

        return $documentTypes;
    }

    /**
     * @return string | null
     */
    public static function findFileForDocumentType($intraTypeFilename, $documentGroupId, $documentTypeId,
                                                   $returnFormat = Colby::returnAbsoluteFilename)
    {
        $intraLibraryFilename = "document-groups/{$documentGroupId}/" .
                           "document-types/{$documentTypeId}/" .
                           "{$intraTypeFilename}";

        return self::findFile($intraLibraryFilename, $returnFormat);
    }

    /**
     * This function searches the website, the Colby system, and the libraries
     * for a file, usually in that order. The behavior of this function is what
     * allows the website to override the behavior of the Colby system and the
     * libraries.
     *
     * @param[in] path
     *
     *  The relative path of the file to be found, for example:
     *
     *  "handlers/handle,view-cart.php"
     *  "setup/update-database.php"
     *  "document-groups/a3...22/document-types/01...96/view.php"
     *
     * @return string | null
     */
    public static function findFile($path, $returnFormat = Colby::returnAbsoluteFilename)
    {
        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            if ($libraryDirectory)
            {
                $intraSiteFilename = "{$libraryDirectory}/{$path}";
            }
            else
            {
                $intraSiteFilename = $path;
            }

            $absoluteFilename = COLBY_SITE_DIRECTORY . "/{$intraSiteFilename}";

            if (is_file($absoluteFilename))
            {
                switch ($returnFormat)
                {
                    case Colby::returnAbsoluteFilename:

                        return $absoluteFilename;

                    case Colby::returnURL:

                        return COLBY_SITE_URL . "/{$intraSiteFilename}";

                    default:

                        throw new InvalidArgumentException('returnFormat');
                }
            }
        }

        return null;
    }

    /**
     * @return string | null
     */
    public static function findHandler($filename, $returnFormat = Colby::returnAbsoluteFilename)
    {
        $path = "handlers/{$filename}";

        return self::findFile($path, $returnFormat);
    }

    /**
     * @return string | null
     */
    public static function findSnippet($filename)
    {
        $path = "snippets/{$filename}";

        return self::findFile($path);
    }

    /**
     * @return array
     */
    public static function globFiles($pattern)
    {
        $filenames = array();

        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            if ($libraryDirectory)
            {
                $intraSitePattern = "{$libraryDirectory}/{$pattern}";
            }
            else
            {
                $intraSitePattern = $pattern;
            }

            $libraryFilenames = glob(COLBY_SITE_DIRECTORY . "/{$intraSitePattern}");

            $filenames = array_merge($filenames, $libraryFilenames);
        }

        return $filenames;
    }

    /**
     * @return array
     */
    public static function globSnippets($pattern)
    {
        $pattern = "snippets/{$pattern}";

        return self::globFiles($pattern);
    }

    /**
     * @return void
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Every handler needs to buffer its output and set its own exception
     * handler so that when an exception is thrown the handler discards its
     * own output buffer and restores the previous exception handler. After
     * that, it can either simply call this function or even rethrow the
     * exception since this is the default exception handler.
     *
     * This process is automated by the `ColbyOutputManager` class, which
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
    public static function handleException($exception, $handlerName = null)
    {
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

        try
        {
            Colby::reportException($exception);

            $absoluteHandlerFilename = null;

            if ($handlerName)
            {
                $absoluteHandlerFilename = Colby::findHandler("handle-exception-{$handlerName}.php");
            }

            if (!$absoluteHandlerFilename)
            {
                $absoluteHandlerFilename = self::findHandler('handle-exception.php');
            }

            if (!$absoluteHandlerFilename)
            {
                /**
                 * Something would have to be wrong with the system
                 * configuration to get here, but if that happens we want to
                 * see the error in the error log otherwise debugging is hard.
                 */

                error_log($exception->getMessage());
            }
            else
            {
                include $absoluteHandlerFilename;
            }
        }
        catch (Exception $rareException)
        {
            error_log('Colby::handleException() RARE EXCEPTION: ' . $rareException->getMessage());
        }
    }

    /**
     * This function should be called only once and is called by the system.
     */
    public static function initialize()
    {
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

        require_once __DIR__ . '/version.php';
        require_once __DIR__ . '/constants.php';
        require_once COLBY_SITE_DIRECTORY . '/version.php';

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

        $colbySystemLibraryDirectory = str_replace(COLBY_SITE_DIRECTORY . '/',
                                                   '',
                                                   COLBY_SYSTEM_DIRECTORY);

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

        /**
         * Include the local configuration file. This file is not checked in
         * and therefore is not shared between different versions of the site.
         * The COLBY_SITE_URL constant is set in this file.
         */

        include_once COLBY_SITE_DIRECTORY . '/colby-configuration.php';

        /**
         * Define COLBY_SYSTEM_URL.
         */

        define('COLBY_SYSTEM_URL', COLBY_SITE_URL . "/$colbySystemLibraryDirectory");

        /**
         * Ensure that any required constants have been set.
         */

        if (!defined('COLBY_SITE_NAME'))
        {
            throw new RuntimeException(
                'The constant `COLBY_SITE_NAME` has not been set.');
        }

        if (!defined('COLBY_SITE_ADMINISTRATOR'))
        {
            throw new RuntimeException(
                'The constant `COLBY_SITE_ADMINISTRATOR` has not been set.');
        }

        if (!defined('COLBY_SITE_IS_BEING_DEBUGGED'))
        {
            throw new RuntimeException(
                'The constant `COLBY_SITE_IS_BEING_DEBUGGED` has not been set.');
        }

        /**
         * Load classes that are used for every request.
         */

        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyArchive.php';

        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyConvert.php';

        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyOutputManager.php';

        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyRequest.php';

        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyUser.php';

        /**
         * Include the site configuration file. This file is checked in and
         * therefore shared between different versions of the site. Shared
         * constants are set and any required libraries are loaded in this file.
         */

        include_once COLBY_SITE_DIRECTORY . '/site-configuration.php';
    }

    /**
     * @return void
     */
    public static function loadLibrary($libraryDirectory)
    {
        $absoluteLibraryDirectory = COLBY_SITE_DIRECTORY . "/{$libraryDirectory}";

        include_once "{$absoluteLibraryDirectory}/version.php";
        include_once "{$absoluteLibraryDirectory}/library-configuration.php";

        self::$libraryDirectories[] = $libraryDirectory;
    }

    /// <summary>
    ///
    /// </summary>
    public static function mysqli()
    {
        if (null === self::$mysqli)
        {
            $mysqli = new mysqli(
                COLBY_MYSQL_HOST,
                COLBY_MYSQL_USER,
                COLBY_MYSQL_PASSWORD,
                COLBY_MYSQL_DATABASE);

            if ($mysqli->connect_error)
            {
                throw new RuntimeException($mysqli->connect_error);
            }

            // The default MySQL character set is "latin1" but the tables use "utf8"

            if (!$mysqli->set_charset('utf8'))
            {
                throw new RuntimeException(
                    'Unable to set mysqli character set to "utf8".');
            }

            self::$mysqli = $mysqli;
        }

        return self::$mysqli;
    }

    ///
    /// simple way to run a query
    ///
    public static function query($sql)
    {
        $mysqli = Colby::mysqli();

        $result = $mysqli->query($sql);

        if ($mysqli->error)
        {
            throw new RuntimeException("MySQL error: {$mysqli->error}\n\n{$sql}");
        }

        return $result;
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
     * @return void
     */
    public static function reportException(Exception $exception)
    {
        try
        {
            /**
             * Report the exception to the error log
             */

            error_log('Exception report for file: ' .
                $exception->getFile() .
                ', line: ' .
                $exception->getLine() .
                ', message: ' .
                $exception->getMessage());

            /**
             * Report the exception via email to the administrator
             */

            if (defined('COLBY_EMAIL_LIBRARY_DIRECTORY') &&
                defined('COLBY_SITE_ERRORS_SEND_EMAILS') &&
                COLBY_SITE_ERRORS_SEND_EMAILS)
            {
                Colby::useEmail();

                $transport = Swift_SmtpTransport::newInstance(COLBY_EMAIL_SMTP_SERVER,
                                                              COLBY_EMAIL_SMTP_PORT,
                                                              COLBY_EMAIL_SMTP_SECURITY);

                $transport->setUsername(COLBY_EMAIL_SMTP_USER);
                $transport->setPassword(COLBY_EMAIL_SMTP_PASSWORD);

                $mailer = Swift_Mailer::newInstance($transport);

                $messageSubject = COLBY_SITE_NAME . ' Error (' . time() . ')';
                $messageFrom = array(COLBY_EMAIL_SENDER => COLBY_EMAIL_SENDER_NAME);
                $messageTo = array(COLBY_SITE_ADMINISTRATOR);
                $messageBody = Colby::exceptionStackTrace($exception);
                $messageBodyHTML = '<pre>' . ColbyConvert::textToHTML($messageBody) . '</pre>';

                $message = Swift_Message::newInstance();
                $message->setSubject($messageSubject);
                $message->setFrom($messageFrom);
                $message->setTo($messageTo);
                $message->setBody($messageBody);
                $message->addPart($messageBodyHTML, 'text/html');

                $mailer->send($message);
            }
        }
        catch (Exception $rareException)
        {
            error_log('Colby::reportException() RARE EXCEPTION: ' . $rareException->getMessage());
        }
    }

    /**
     *
     */
    public static function siteSchemaVersionNumber()
    {
        $sql = 'SELECT ColbySiteSchemaVersionNumber() as `versionNumber`';

        try
        {
            $result = Colby::query($sql);
        }
        catch (Exception $exception)
        {
            if (1305 == Colby::mysqli()->errno)
            {
                /**
                 * If the `ColbySiteSchemaVersionNumber` function doesn't exist
                 * that's equivalent to a site schema version number of 0.
                 */

                return 0;
            }
            else
            {
                throw $exception;
            }
        }

        $versionNumber = $result->fetch_object()->versionNumber;

        $result->free();

        return $versionNumber;
    }

    /**
     *
     */
    public static function setSiteSchemaVersionNumber($versionNumber)
    {
        $sql = 'DROP FUNCTION ColbySiteSchemaVersionNumber';

        try
        {
            Colby::query($sql);
        }
        catch (Exception $exception)
        {
            if (1305 != Colby::mysqli()->errno)
            {
                /**
                 * If the `ColbySiteSchemaVersionNumber` function doesn't exist
                 * it's okay because we are about to create it; otherwise, we
                 * throw the exception.
                 */

                throw $exception;
            }
        }

        $versionNumber = intval($versionNumber);

        $sql = <<<EOT
CREATE FUNCTION ColbySiteSchemaVersionNumber()
RETURNS BIGINT UNSIGNED
BEGIN
    RETURN {$versionNumber};
END
EOT;

        Colby::query($sql);
    }

    /**
     * @return string
     *  A unique SHA-1 hash in hexadecimal.
     *  Example: '90027a5ca28cb5301febdc1f31db512dc663c944'
     */
    public static function uniqueSHA1Hash()
    {
        $time = microtime();
        $rand = rand();
        $i = self::$uniqueHashCounter;

        $hash = sha1("i:{$i} t:{$time} r:{$rand}");

        self::$uniqueHashCounter++;

        return $hash;
    }

    /**
     * Enable and initialize Swift Mailer email services.
     */
    public static function useEmail()
    {
        /**
         * Email is a heavyweight service. Code that wants to use email
         * should check to see if the service is available first or be
         * part of a library that ensures email services are available.
         */

        if (!defined('COLBY_EMAIL_LIBRARY_DIRECTORY'))
        {
            throw new RuntimeException('Email service was requested but has not been configured for this website.');
        }

        include_once COLBY_EMAIL_LIBRARY_DIRECTORY . '/lib/swift_required.php';
    }

    /**
     * Includes the classes that are needed to do image processing.
     *
     * @return void
     */
    public static function useImage()
    {
        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyGeometry.php';
        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyImageResizer.php';
        include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyImageUploader.php';
    }
}
