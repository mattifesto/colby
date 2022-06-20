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
include cbsysdir() . '/classes/CBID/CBID.php';
include cbsysdir() . '/classes/CBConvert/CBConvert.php';

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



    /**
     * @return void
     */
    static function createSettingsFile(): void {
        $settingsFilepath = (
            cbsitedir() .
            '/settings.json'
        );

        $developerPasswordHash = password_hash(
            ColbyInstaller::$properties->developerPassword,
            PASSWORD_DEFAULT
        );

        $settingsObject = (object)[
            'developerEmailAddress' => (
                ColbyInstaller::$properties->developerEmailAddress
            ),

            'developerPasswordHash' => $developerPasswordHash,
        ];

        file_put_contents(
            $settingsFilepath,
            json_encode(
                $settingsObject
            )
        );
    }
    /* createSettingsFile() */



    /**
     * @return void
     */
    static function handleError(
        $severity,
        $message,
        $file,
        $line
    ): void {
        throw new ErrorException(
            $message,
            0,
            $severity,
            $file,
            $line
        );
    }
    /* handleError() */



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

        ColbyInstaller::$dataDirectory =
        cbsitedir() . '/data';

        ColbyInstaller::$tmpDirectory =
        cbsitedir() . '/tmp';

        ColbyInstaller::$colbyConfigurationFilename =
        cbsitedir() . '/colby-configuration.php';

        ColbyInstaller::$colbyFilename =
        cbsitedir() . '/colby.php';

        ColbyInstaller::$faviconGifFilename =
        cbsitedir() . '/favicon.gif';

        ColbyInstaller::$faviconIcoFilename =
        cbsitedir() . '/favicon.ico';

        ColbyInstaller::$gitignoreFilename =
        cbsitedir() . '/.gitignore';

        ColbyInstaller::$HTAccessFilename =
        cbsitedir() . '/.htaccess';

        ColbyInstaller::$indexFilename =
        cbsitedir() . '/index.php';

        ColbyInstaller::$siteConfigurationFilename =
        cbsitedir() . '/site-configuration.php';

        ColbyInstaller::$versionFilename =
        cbsitedir() . '/version.php';


        if (ColbyInstaller::$propertiesAreAllSet) {
            try {
                ColbyInstaller::verifyFormProperties();
                ColbyInstaller::install();
            } catch (Throwable $exception) {
                ColbyInstaller::$exception = $exception;

                ColbyInstaller::renderPlan();
            }
        } else {
            ColbyInstaller::renderPlan();
        }
    }
    /* initialize() */



    /**
     * Set the ColbyInstaller::$properties variable
     *
     * @return null
     */
    static function initializeProperties() {
        $siteClassPrefix = trim(
            cb_post_value('siteClassPrefix', '')
        );

        if (empty($siteClassPrefix)) {
            $siteClassPrefix = 'CBX';
        }

        $p = (object)[
            'siteDomainName' => cb_post_value(
                'siteDomainName',
                $_SERVER['SERVER_NAME'],
                'trim'
            ),
            'siteClassPrefix' => $siteClassPrefix,
            'mysqlHost' => cb_post_value('mysqlHost', '', 'trim'),
            'mysqlUser' => cb_post_value('mysqlUser', '', 'trim'),
            'mysqlPassword' => cb_post_value('mysqlPassword', '', 'trim'),
            'mysqlDatabase' => cb_post_value('mysqlDatabase', '', 'trim'),

            'developerEmailAddress' => trim(
                cb_post_value(
                    'developerEmailAddress',
                    ''
                )
            ),

            'developerPassword' => cb_post_value(
                'developerPassword',
                ''
            ),

            'developerPassword2' => cb_post_value(
                'developerPassword2',
                ''
            ),
        ];

        ColbyInstaller::$properties = $p;

        ColbyInstaller::$propertiesAreAllSet = (
            !empty($p->siteDomainName) &&
            !empty($p->mysqlHost) &&
            !empty($p->mysqlUser) &&
            !empty($p->mysqlPassword) &&
            !empty($p->mysqlDatabase) &&
            !empty($p->developerEmailAddress) &&
            !empty($p->developerPassword) &&
            !empty($p->developerPassword2)
        );
    }
    /* initializeProperties() */



    /**
     * @return void
     */
    static function install(): void {
        try {
            $siteDirectory = cbsitedir();
            $templateDirectory = __DIR__ . '/templates';


            /* verify MySQL login properties */

            $mysqliDriver = new mysqli_driver();
            $mysqliDriver->report_mode = MYSQLI_REPORT_STRICT;

            $mysqli = new mysqli(
                ColbyInstaller::$properties->mysqlHost,
                ColbyInstaller::$properties->mysqlUser,
                ColbyInstaller::$properties->mysqlPassword,
                ColbyInstaller::$properties->mysqlDatabase
            );

            if ($mysqli->connect_error) {
                throw new Exception($mysqli->connect_error);
            }

            if (!$mysqli->set_charset('utf8mb4')) {
                throw new Exception(
                    'Unable to set the mysqli character set to UTF-8.'
                );
            }

            $mysqli->query(
                'CREATE TABLE `ColbyInstallationTest` (`title` VARCHAR(80))'
            );

            if ($mysqli->error) {
                throw new Exception(
                    "The MySQL account provided is unable to create " .
                    "tables: {$mysqli->error}"
                );
            }

            $mysqli->query(
                'DROP TABLE `ColbyInstallationTest`'
            );

            if ($mysqli->error) {
                throw new Exception(
                    "The MySQL account provided is unable to drop " .
                    "tables: {$mysqli->error}"
                );
            }

            $mysqli->close();


            /* Create files and directories */

            ColbyInstaller::createSettingsFile();

            if (!is_dir(ColbyInstaller::$dataDirectory)) {
                mkdir(ColbyInstaller::$dataDirectory);
            }

            if (!is_dir(ColbyInstaller::$tmpDirectory)) {
                mkdir(ColbyInstaller::$tmpDirectory);
            }

            ColbyInstaller::writeColbyConfiguration();

            if (!is_file(ColbyInstaller::$gitignoreFilename)) {
                copy(
                    __DIR__ . '/gitignore.template.data',
                    ColbyInstaller::$gitignoreFilename
                );
            } else {
                $ignoresOld = file(ColbyInstaller::$gitignoreFilename);
                $ignoresNew = file(__DIR__ . '/gitignore.template.data');
                $ignores = array_merge($ignoresOld, $ignoresNew);
                $ignores = array_unique($ignores);

                sort($ignores);

                $ignores = implode('', $ignores);

                file_put_contents(ColbyInstaller::$gitignoreFilename, $ignores);
            }

            touch(ColbyInstaller::$HTAccessFilename);

            if (ColbyInstaller::shouldPerformAFullInstallation()) {
                $newData = file_get_contents(
                    __DIR__ . '/htaccess.template.data'
                );

                file_put_contents(
                    ColbyInstaller::$HTAccessFilename,
                    $newData,
                    FILE_APPEND
                );
            } else {
                $newData = file_get_contents(
                    __DIR__ . '/htaccess.partial.template.data'
                );

                file_put_contents(
                    ColbyInstaller::$HTAccessFilename,
                    $newData,
                    FILE_APPEND
                );
            }

            if (ColbyInstaller::shouldPerformAFullInstallation()) {
                copy(
                    __DIR__ . '/index.template.data',
                    ColbyInstaller::$indexFilename
                );
            } else {
                copy(
                    __DIR__ . '/index.template.data',
                    ColbyInstaller::$colbyFilename
                );
            }

            if (!is_file(ColbyInstaller::$siteConfigurationFilename)) {
                copy(
                    __DIR__ . '/site-configuration.template.data',
                    ColbyInstaller::$siteConfigurationFilename
                );
            }

            if (!is_file(ColbyInstaller::$versionFilename)) {
                copy(
                    __DIR__ . '/version.template.data',
                    ColbyInstaller::$versionFilename
                );
            }

            if (!is_file(ColbyInstaller::$faviconGifFilename)) {
                touch(ColbyInstaller::$faviconGifFilename);
            }

            if (!is_file(ColbyInstaller::$faviconIcoFilename)) {
                touch(ColbyInstaller::$faviconIcoFilename);
            }

            /* /classes */

            $destdir = "{$siteDirectory}/classes";

            if (!is_dir($destdir)) {
                mkdir($destdir);
            }

            $prefix = ColbyInstaller::$properties->siteClassPrefix;

            $templates = [
                ['BlogPostPageKind', 'php'],
                ['BlogPostPageTemplate', 'php'],
                ['Menu_main', 'php'],
                ['PageFooterView', 'php'],
                ['PageFooterView', 'css'],
                ['PageFrame', 'php'],
                ['PageHeaderView', 'php'],
                ['PageSettings', 'php'],
                ['PageTemplate', 'php'],
                ['Page_blog', 'php'],
            ];

            foreach ($templates as $template) {
                $templateName = $template[0];
                $extension = $template[1];

                $sourceFilepath =
                "{$templateDirectory}/{$templateName}.{$extension}";

                $destinationDirectory =
                "{$siteDirectory}/classes/{$prefix}{$templateName}";

                $destinationFilepath =
                "{$destinationDirectory}/{$prefix}{$templateName}.{$extension}";

                if (!is_dir($destinationDirectory)) {
                    mkdir($destinationDirectory);
                }

                $content = file_get_contents($sourceFilepath);
                $content = preg_replace('/PREFIX/', $prefix, $content);

                $content = preg_replace_callback(
                    '/RANDOMID/',
                    function () {
                        $ID = CBID::generateRandomCBID();
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
    /* install() */



    /**
     * @return void
     */
    static function renderPageBegin(): void {
        $fontURL = 'https://fonts.googleapis.com/css?family=Open+Sans:400,700';

        ?>
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Colby Installation</title>
                <link
                    rel="stylesheet"
                    href="<?= $fontURL ?>"
                >
                <link
                    rel="stylesheet"
                    href="/colby/setup/styles.css"
                >
            </head>
            <body class="ColbyInstaller">
                <main>
        <?php
    }
    /* renderPageBegin() */



    /**
     * @return void
     */
    static function renderPageEnd(): void {
        ?>
                </main>
            </body>
        </html>
        <?php
    }
    /* renderPageEnd() */



    /**
     * @return void
     */
    static function renderPlan(): void {
        ColbyInstaller::renderPageBegin();

        include __DIR__ . '/install-page.php';

        ColbyInstaller::renderPageEnd();
    }
    /* renderPlan() */



    /**
     * This function is called if this page is accessed through HTTP instead of
     * HTTPS. A secure connection is required to set up Colby.
     *
     * @return void
     */
    static function renderSecurityWarning(): void {
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
    /* renderSecurityWarning() */



    /**
     * This function is called if the Colby submodule has not been propertly
     * initialized.
     *
     * @return void
     */
    static function renderSubmoduleWarning(): void {
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
    /* renderSubmoduleWarning() */



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
    private static function shouldPerformAFullInstallation(): bool {
        return !is_file(ColbyInstaller::$indexFilename);
    }



    /**
     * This function is called before ColbyInstaller::install(). If any of the
     * values the developer entered on the form are not valid this function
     * will throw an exception.
     */
    private static function verifyFormProperties(): void {
        /* verify developer email address */

        $developerEmailAddress = CBConvert::valueAsEmail(
            ColbyInstaller::$properties->developerEmailAddress
        );

        if ($developerEmailAddress === null) {
            throw new Exception(
                'The developer email address is not valid.'
            );
        }


        /* verify developer password */

        if (
            ColbyInstaller::$properties->developerPassword !==
            ColbyInstaller::$properties->developerPassword2
        ) {
            throw new Exception(
                'The developer passwords don\'t match.'
            );
        }
    }
    /* verifyFormProperties() */



    /**
     * @return void
     */
    static function writeColbyConfiguration(): void {
        if (is_file(ColbyInstaller::$colbyConfigurationFilename)) {
            return;
        }

        $p = ColbyInstaller::$properties;
        $siteDomainName = addslashes($p->siteDomainName);

        $mysqlHost = addslashes($p->mysqlHost);
        $mysqlUser = addslashes($p->mysqlUser);
        $mysqlPassword = addslashes($p->mysqlPassword);
        $mysqlDatabase = addslashes($p->mysqlDatabase);

        $encryptionPassword = bin2hex(
            openssl_random_pseudo_bytes(20)
        );

        $content = <<<EOT
        <?php

        define(
            'CBSiteURL', /* allowed */
            'https://{$siteDomainName}'
        );

        define(
            'CBMySQLHost',
            '{$mysqlHost}'
        );

        define(
            'CBMySQLUser',
            '{$mysqlUser}'
        );

        define(
            'CBMySQLPassword',
            '{$mysqlPassword}'
        );

        define(
            'CBMySQLDatabase',
            '{$mysqlDatabase}'
        );

        define(
            'CBEncryptionPassword',
            '{$encryptionPassword}'
        );

        EOT;

        file_put_contents(
            ColbyInstaller::$colbyConfigurationFilename,
            $content
        );
    }
    /* writeColbyConfiguration() */

}
