<?php

require_once(__DIR__ . '/../colby-configuration.php');

class Colby
{
    // mysqli
    // This holds the mysqli object if the request needs database access.

    private static $mysqli = null;

    // libraryDirectories
    // A list of root relative directories to be search when looking for
    // snippets, handlers, or document related files.

    public static $libraryDirectories = array();

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
     * @return bool
     */
    public static function isReadableFile($absoluteFilename)
    {
        // is_readable: file or directory exists and is readable
        // is_file: file is a regular file (not a directory)

        return (is_readable($absoluteFilename) && is_file($absoluteFilename));
    }

    /**
     * @return array
     *  An array of objects containing properties for each available document
     *  group.
     */
    public static function findDocumentGroups()
    {
        $groups = array();

        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            $metadataFilenames = glob(
                COLBY_SITE_DIRECTORY .
                "/{$libraryDirectory}/document-groups/*/document-group.data");

            $matchExpression =
                '/^' .
                addcslashes(COLBY_SITE_DIRECTORY, '/') .
                '\/((.*?)\/)?' .
                'document-groups\/(.*?)\//';

            foreach ($metadataFilenames as $metadataFilename)
            {
                preg_match($matchExpression, $metadataFilename, $matches);

                $location = $matches[2];
                $groupId = $matches[3];

                $o = new stdClass();

                $o->id = $groupId;
                $o->location = $location;
                $o->metadataFilename = $metadataFilename;
                $o->metadata = unserialize(file_get_contents($metadataFilename));

                $groups[] = $o;
            }
        }

        return $groups;
    }

    /**
     * @return string | false
     */
    public static function findFileForDocumentGroup($filename, $documentGroupId)
    {
        $relativeFilename = "/document-groups/{$documentGroupId}/{$filename}";

        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            $absoluteFilename = COLBY_SITE_DIRECTORY . "/{$libraryDirectory}/{$relativeFilename}";

            if (self::isReadableFile($absoluteFilename))
            {
                return $absoluteFilename;
            }
        }

        return false;
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

            $matchExpression =
                '/^' .
                addcslashes(COLBY_SITE_DIRECTORY, '/') .
                '\/((.*?)\/)?' .
                "document-groups\/{$documentGroupId}" .
                '\/document-types\/(.*?)\//';

            foreach ($metadataFilenames as $metadataFilename)
            {
                preg_match($matchExpression, $metadataFilename, $matches);

                $location = $matches[2];
                $documentTypeId = $matches[3];

                $o = new stdClass();

                $o->id = $documentTypeId;
                $o->location = $location;
                $o->metadataFilename = $metadataFilename;
                $o->metadata = unserialize(file_get_contents($metadataFilename));

                $documentTypes[] = $o;
            }
        }

        return $documentTypes;
    }

    /**
     * @return string | false
     */
    public static function findFileForDocumentType($filename, $documentGroupId, $documentTypeId)
    {
        $relativeFilename = "/document-groups/{$documentGroupId}" .
                            "/document-types/{$documentTypeId}" .
                            "/{$filename}";

        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            $absoluteFilename = COLBY_SITE_DIRECTORY . "/{$libraryDirectory}/{$relativeFilename}";

            if (self::isReadableFile($absoluteFilename))
            {
                return $absoluteFilename;
            }
        }

        return false;
    }

    /**
     * @return string | false
     */
    public static function findHandler($filename)
    {
        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            $handlerFilename = COLBY_SITE_DIRECTORY . "/{$libraryDirectory}/handlers/{$filename}";

            if (self::isReadableFile($handlerFilename))
            {
                return $handlerFilename;
            }
        }

        return false;
    }

    /**
     * @return string | false
     */
    public static function findSnippet($filename)
    {
        foreach (self::$libraryDirectories as $libraryDirectory)
        {
            $snippetFilename = COLBY_SITE_DIRECTORY . "/{$libraryDirectory}/snippets/{$filename}";

            if (self::isReadableFile($snippetFilename))
            {
                return $snippetFilename;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public static function globSnippets($pattern)
    {
        $snippetFilenames = array();

        foreach (self::$libraryDirectories as $libraryDirectory)
        {

            $filenames = glob(COLBY_SITE_DIRECTORY . "/{$libraryDirectory}/snippets/{$pattern}");

            $snippetFilenames = array_merge($snippetFilenames, $filenames);
        }

        return $snippetFilenames;
    }

    /**
     * @return void
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * @return void
     */
    public static function handleException($exception, $handlerName = null)
    {
        // exception handlers should never throw exceptions
        // if they do, it's very difficult to debug
        // in some cases while making more major system changes
        // throwing exceptions in exception handlers is unavoidable
        //     errors are converted to exceptions in Colby
        //     so even if we don't throw, exceptions can occur
        // so to be sure we always get some good feedback
        // we wrap all code in an exception handler in a try-catch block
        // and use error_log as a last resort to find out what went wrong
        // if an exception is thrown inside this exception handler

        try
        {
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
                 * 2013.03.19
                 *
                 * A situation occurred where COLBY_DIRECTORY wasn't set by the
                 * configuration file which meant that an exception handler file
                 * was not found. If this happens again, the exception message
                 * should be sent to the error log. It's possible that other
                 * configuration issues might also trigger this situation and
                 * they are very difficult to debug without this code.
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
            error_log($rareException->getMessage());
        }
    }

    ///
    /// this function should be run only once
    /// it is run automatically when Colby is first included
    ///
    public static function initialize()
    {
        // Colby sites always run with all error reporting turned on
        // in the worst case scenario
        // the production environment will have newer PHP
        // which may lead to surprise errors
        // but those errors should still be fixed in the dev environment

        error_reporting(E_ALL | E_STRICT);

        set_error_handler('Colby::handleError');
        set_exception_handler('Colby::handleException');

        if (!defined('COLBY_SITE_DIRECTORY'))
        {
            throw new RuntimeException(
                'The constant `COLBY_SITE_DIRECTORY` has not been set.');
        }

        if (!defined('COLBY_SITE_URL'))
        {
            throw new RuntimeException(
                'The constant `COLBY_SITE_URL` has not been set.');
        }

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

        if (!defined('COLBY_DIRECTORY'))
        {
            throw new RuntimeException(
                'The constant `COLBY_DIRECTORY` has not been set.' .
                'Colby\'s `version.php` should be included in the site\'s `colby-configuration.php` file.');
        }

        // Add the website and Colby library directories. They are unshifted
        // onto the beginning of the array because they should be consulted
        // before other libraries.
        //
        // The order is:
        //
        //    COLBY_SITE_DIRECTORY
        //    COLBY_DIRECTORY
        //    added directory 1
        //    added directory 2
        //    ...

        array_unshift(self::$libraryDirectories, 'colby');
        array_unshift(self::$libraryDirectories, '');

        // the order of these files might matter some day
        // files that depend on other files should be included after
        // at this time, none of these files depends on another
        // so they are in alphabetical order

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyArchive.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyConvert.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyOutputManager.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyPageModel.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyRequest.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyUser.php');

        ColbyRequest::handleRequest();
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
            throw new RuntimeException("MySQL error: {$mysqli->error}");
        }

        return $result;
    }

    //
    //
    //
    public static function useImage()
    {
        include_once(COLBY_SITE_DIRECTORY .
            '/colby/classes/ColbyImage.php');
    }

    //
    //
    //
    public static function useRect()
    {
        include_once(COLBY_SITE_DIRECTORY .
            '/colby/classes/ColbyRect.php');
    }
}

Colby::initialize();
