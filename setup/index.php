<?php

set_error_handler('ColbyInstaller::handleError');

/**
 * @return string
 */
function cbsitedir(): string {
    return $_SERVER['DOCUMENT_ROOT'];
}

function cbsysdir(): string {
    return cbsitedir() . '/colby';
}

include cbsysdir() . '/functions.php';
include cbsysdir() . '/classes/CBHex160/CBHex160.php';

ColbyInstaller::initialize();

class ColbyInstaller {

    private static $exception;
    private static $properties;
    private static $propertiesAreAllSet = false;

    private static $dataDirectory;
    private static $tmpDirectory;

    private static $colbyConfigurationFilename;
    private static $colbyFilename;
    private static $faviconGifFilename;
    private static $faviconIcoFilename;
    private static $gitignoreFilename;
    private static $HTAccessFilename;
    private static $indexFilename;
    private static $siteConfigurationFilename;
    private static $versionFilename;

    static function handleError($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * @return void
     */
    static function initialize() {
        if (empty($_SERVER['HTTPS'])) {
            ColbyInstaller::renderSecurityWarning();

            return;
        }

        if (!is_file(cbsitedir() . '/colby/classes/Parsedown/Parsedown.php')) {
            ColbyInstaller::renderSubmoduleWarning();

            return;
        }

        ColbyInstaller::initializeProperties();

        self::$dataDirectory                = cbsitedir() . '/data';
        self::$tmpDirectory                 = cbsitedir() . '/tmp';

        self::$colbyConfigurationFilename   = cbsitedir() . '/colby-configuration.php';
        self::$colbyFilename                = cbsitedir() . '/colby.php';
        self::$faviconGifFilename           = cbsitedir() . '/favicon.gif';
        self::$faviconIcoFilename           = cbsitedir() . '/favicon.ico';
        self::$gitignoreFilename            = cbsitedir() . '/.gitignore';
        self::$HTAccessFilename             = cbsitedir() . '/.htaccess';
        self::$indexFilename                = cbsitedir() . '/index.php';
        self::$siteConfigurationFilename    = cbsitedir() . '/site-configuration.php';
        self::$versionFilename              = cbsitedir() . '/version.php';


        if (ColbyInstaller::$propertiesAreAllSet) {
            try {
                self::install();
            } catch (Throwable $exception) {
                ColbyInstaller::$exception = $exception;

                self::renderPlan();
            }
        } else {
            self::renderPlan();
        }
    }

    /**
     * Set the ColbyInstaller::$properties variable
     *
     * @return null
     */
    static function initializeProperties() {
        $siteClassPrefix = trim(cb_post_value('siteClassPrefix', ''));

        if (empty($siteClassPrefix)) {
            $siteClassPrefix = 'CBX';
        }

        $p = (object)[
            'siteDomainName' => cb_post_value('siteDomainName', $_SERVER['SERVER_NAME'], 'trim'),
            'siteClassPrefix' => $siteClassPrefix,
            'mysqlHost' => cb_post_value('mysqlHost', '', 'trim'),
            'mysqlUser' => cb_post_value('mysqlUser', '', 'trim'),
            'mysqlPassword' => cb_post_value('mysqlPassword', '', 'trim'),
            'mysqlDatabase' => cb_post_value('mysqlDatabase', '', 'trim'),
            'facebookAppID' => cb_post_value('facebookAppID', '', 'trim'),
            'facebookAppSecret' => cb_post_value('facebookAppSecret', '', 'trim'),
        ];

        ColbyInstaller::$properties = $p;

        ColbyInstaller::$propertiesAreAllSet =
            !empty($p->siteDomainName) &&
            !empty($p->mysqlHost) &&
            !empty($p->mysqlUser) &&
            !empty($p->mysqlPassword) &&
            !empty($p->mysqlDatabase) &&
            !empty($p->facebookAppID) &&
            !empty($p->facebookAppSecret);
    }

    /**
     * @return null
     */
    static function install() {
        try {
            $siteDirectory = cbsitedir();
            $templateDirectory = __DIR__ . '/templates';

            /* Verify MySQL login properties */

            $mysqliDriver = new mysqli_driver();
            $mysqliDriver->report_mode = MYSQLI_REPORT_STRICT;

            $mysqli = new mysqli(
                self::$properties->mysqlHost,
                self::$properties->mysqlUser,
                self::$properties->mysqlPassword,
                self::$properties->mysqlDatabase
            );

            if ($mysqli->connect_error) {
                throw new Exception($mysqli->connect_error);
            }

            if (!$mysqli->set_charset('utf8mb4')) {
                throw new Exception( 'Unable to set the mysqli character set to UTF-8.');
            }

            $mysqli->query('CREATE TABLE `ColbyInstallationTest` (`title` VARCHAR(80))');

            if ($mysqli->error) {
                throw new Exception("The MySQL account provided is unable to create tables: {$mysqli->error}");
            }

            $mysqli->query('DROP TABLE `ColbyInstallationTest`');

            if ($mysqli->error) {
                throw new Exception("The MySQL account provided is unable to drop tables: {$mysqli->error}");
            }

            $mysqli->close();

            /* Create files and directories */

            if (!is_dir(self::$dataDirectory)) {
                mkdir(self::$dataDirectory);
            }

            if (!is_dir(self::$tmpDirectory)) {
                mkdir(self::$tmpDirectory);
            }

            ColbyInstaller::writeColbyConfiguration();

            if (!is_file(self::$gitignoreFilename)) {
                copy(__DIR__ . '/gitignore.template.data', self::$gitignoreFilename);
            } else {
                $ignoresOld         = file(self::$gitignoreFilename);
                $ignoresNew         = file(__DIR__ . '/gitignore.template.data');
                $ignores            = array_merge($ignoresOld, $ignoresNew);
                $ignores            = array_unique($ignores);

                sort($ignores);

                $ignores            = implode('', $ignores);

                file_put_contents(self::$gitignoreFilename, $ignores);
            }

            touch(self::$HTAccessFilename);

            if (self::shouldPerformAFullInstallation()) {
                $newData = file_get_contents(__DIR__ . '/htaccess.template.data');

                file_put_contents(self::$HTAccessFilename, $newData, FILE_APPEND);
            } else {
                $newData = file_get_contents(__DIR__ . '/htaccess.partial.template.data');

                file_put_contents(self::$HTAccessFilename, $newData, FILE_APPEND);
            }

            if (self::shouldPerformAFullInstallation()) {
                copy(__DIR__ . '/index.template.data', self::$indexFilename);
            } else {
                copy(__DIR__ . '/index.template.data', self::$colbyFilename);
            }

            if (!is_file(self::$siteConfigurationFilename)) {
                copy(__DIR__ . '/site-configuration.template.data', self::$siteConfigurationFilename);
            }

            if (!is_file(self::$versionFilename)) {
                copy(__DIR__ . '/version.template.data', self::$versionFilename);
            }

            if (!is_file(self::$faviconGifFilename)) {
                touch(self::$faviconGifFilename);
            }

            if (!is_file(self::$faviconIcoFilename)) {
                touch(self::$faviconIcoFilename);
            }

            /* /classes */

            $destdir = "{$siteDirectory}/classes";

            if (!is_dir($destdir)) {
                mkdir($destdir);
            }

            $prefix = ColbyInstaller::$properties->siteClassPrefix;
            $templates = [
                ['BlogPage', 'php'],
                ['BlogPostPageKind', 'php'],
                ['BlogPostPageTemplate', 'php'],
                ['MainMenu', 'php'],
                ['PageFooterView', 'php'],
                ['PageFooterView', 'css'],
                ['PageFrame', 'php'],
                ['PageHeaderView', 'php'],
                ['PageSettings', 'php'],
                ['PageTemplate', 'php'],
            ];

            foreach ($templates as $template) {
                $templateName = $template[0];
                $extension = $template[1];
                $sourceFilepath = "{$templateDirectory}/{$templateName}.{$extension}";
                $destinationDirectory = "{$siteDirectory}/classes/{$prefix}{$templateName}";
                $destinationFilepath = "{$destinationDirectory}/{$prefix}{$templateName}.{$extension}";

                if (!is_dir($destinationDirectory)) {
                    mkdir($destinationDirectory);
                }

                $content = file_get_contents($sourceFilepath);
                $content = preg_replace('/PREFIX/', $prefix, $content);
                $content = preg_replace_callback(
                    '/RANDOMID/',
                    function () {
                        $ID = CBHex160::random();
                        return "'{$ID}'";
                    },
                    $content
                );
                file_put_contents($destinationFilepath, $content);
            }
        } catch (Throwable $throwable) {
            ColbyInstaller::renderPageBegin();
            echo '<p>An error occurred in the setup script.';
            echo '<p>' . $throwable->getMessage();
            echo '<p>in ' . $throwable->getFile();
            echo '<p>at line ' . $throwable->getLine();
            ColbyInstaller::renderPageEnd();

            exit;
        }

        header('Location: /admin/');
    }

    /**
     * @return null
     */
    static function renderPageBegin() {
        ?>
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Colby Installation</title>
                <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,700">
                <link rel="stylesheet" href="/colby/setup/styles.css">
            </head>
            <body class="ColbyInstaller">
                <main>
        <?php
    }

    /**
     * @return null
     */
    static function renderPageEnd() {
        ?>
                </main>
            </body>
        </html>
        <?php
    }

    /**
     * @return null
     */
    static function renderPlan() {
        ColbyInstaller::renderPageBegin();

        include __DIR__ . '/install-page.php';

        ColbyInstaller::renderPageEnd();
    }

    /**
     * This function is called if this page is accessed through HTTP instead of
     * HTTPS. A secure connection is required to set up Colby.
     */
    static function renderSecurityWarning() {
        $domainName = $_SERVER['SERVER_NAME'];
        $link = "https://{$domainName}/colby/setup/";

        ColbyInstaller::renderPageBegin();

        ?>

        <h1>Colby Requires HTTPS</h1>
        <p>The Colby system requires all websites to run over HTTPS. Once Colby
           has been set up, it will automatically redirect all HTTP requests to
           HTTPS.
        <p>Either replace the http:// in this URL with https:// in your browser
           and reload this page or reload with the link below:
        <p><a href="<?= $link ?>"><?= $link ?></a>

        <?php

        ColbyInstaller::renderPageEnd();
    }

    /**
     * This function is called if the Colby submodule has not been propertly
     * initialized.
     */
    static function renderSubmoduleWarning() {
        ColbyInstaller::renderPageBegin();

        ?>

        <h1>
        Colby submodule initialization
        </h1>

        <p>
        The Colby submodule has not been fully initialized. In a terminal type
        the following line in the <code><?= cbsitedir() ?></code> directory and
        reload this page.

        <p>
        <code>git submodule update --init --recursive</code>

        <?php

        ColbyInstaller::renderPageEnd();
    }

    /**
     * This function determines whether a full or partial installation should
     * occur. In the future this function may do more detailed analysis to
     * determine the correct course of action.
     *
     * For now, if the `index.php` file exists, this will be a partial
     * installation. Otherwise it will be full installation.
     *
     * @return bool
     */
    private static function shouldPerformAFullInstallation() {
        return !is_file(self::$indexFilename);
    }

    /**
     * @return null
     */
    static function writeColbyConfiguration() {
        if (is_file(self::$colbyConfigurationFilename)) {
            return;
        }

        $p = ColbyInstaller::$properties;
        $siteDomainName = addslashes($p->siteDomainName);

        $facebookAppID = addslashes($p->facebookAppID);
        $facebookAppSecret = addslashes($p->facebookAppSecret);

        $mysqlHost = addslashes($p->mysqlHost);
        $mysqlUser = addslashes($p->mysqlUser);
        $mysqlPassword = addslashes($p->mysqlPassword);
        $mysqlDatabase = addslashes($p->mysqlDatabase);

        $encryptionPassword = bin2hex(openssl_random_pseudo_bytes(20));

        $content = <<<EOT
<?php

define('CBSiteURL',                         'https://{$siteDomainName}');

define('CBFacebookAppID',                   '{$facebookAppID}');
define('CBFacebookAppSecret',               '{$facebookAppSecret}');

define('CBMySQLHost',                       '{$mysqlHost}');
define('CBMySQLUser',                       '{$mysqlUser}');
define('CBMySQLPassword',                   '{$mysqlPassword}');
define('CBMySQLDatabase',                   '{$mysqlDatabase}');

define('CBEncryptionPassword',              '{$encryptionPassword}');

EOT;

        file_put_contents(ColbyInstaller::$colbyConfigurationFilename, $content);
    }
}
