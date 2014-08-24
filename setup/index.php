<?php

define('CBSiteDirectory', $_SERVER['DOCUMENT_ROOT']);

ColbyInstaller::initialize();

class ColbyInstaller {

    private static $dataDirectory;
    private static $tmpDirectory;

    private static $colbyConfigurationFilename;
    private static $faviconGifFilename;
    private static $faviconIcoFilename;
    private static $gitignoreFilename;
    private static $htaccessFilename;
    private static $indexFilename;
    private static $siteConfigurationFilename;
    private static $versionFilename;

    private static $shouldPerformInstallation;

    /**
     * @return void
     */
    public static function initialize() {

        self::$dataDirectory                = CBSiteDirectory . '/data';
        self::$tmpDirectory                 = CBSiteDirectory . '/tmp';

        self::$colbyConfigurationFilename   = CBSiteDirectory . '/colby-configuration.php';
        self::$faviconGifFilename           = CBSiteDirectory . '/favicon.gif';
        self::$faviconIcoFilename           = CBSiteDirectory . '/favicon.ico';
        self::$gitignoreFilename            = CBSiteDirectory . '/.gitignore';
        self::$htaccessFilename             = CBSiteDirectory . '/.htaccess';
        self::$indexFilename                = CBSiteDirectory . '/index.php';
        self::$siteConfigurationFilename    = CBSiteDirectory . '/site-configuration.php';
        self::$versionFilename              = CBSiteDirectory . '/version.php';

        self::$shouldPerformInstallation    = (isset($_GET['install']) && $_GET['install'] == 'true');

        if (self::$shouldPerformInstallation) {

            self::install();

        } else {

            self::renderPlan();
        }
    }

    /**
     * @return void
     */
    public static function install() {

        if (file_exists(self::$dataDirectory) ||
            file_exists(self::$colbyConfigurationFilename) ||
            file_exists(self::$gitignoreFilename) ||
            file_exists(self::$htaccessFilename) ||
            file_exists(self::$indexFilename) ||
            file_exists(self::$siteConfigurationFilename) ||
            file_exists(self::$versionFilename)) {

            // if .htaccess exists correctly in the web root directory
            // the user will not even be able to load this file
            // so if any of the above files exist
            // it means that the install is only partially complete
            // and that shouldn't really ever even happen so just show a message

            ?>

            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Colby Setup</title>
                    <meta name="description" content="This page is meant to bootstrap a fresh install of a Colby based website.">
                </head>
                <body>
                    <h1>Colby Setup</h1>
                    <p>This site has been partially setup and is in an unknown state.
                </body>
            </html>

            <?php

        } else {

            mkdir(self::$dataDirectory);
            copy(__DIR__ . '/colby-configuration.template.data', self::$colbyConfigurationFilename);
            copy(__DIR__ . '/gitignore.template.data', self::$gitignoreFilename);
            copy(__DIR__ . '/htaccess.template.data', self::$htaccessFilename);
            copy(__DIR__ . '/index.template.data', self::$indexFilename);
            copy(__DIR__ . '/site-configuration.template.data', self::$siteConfigurationFilename);
            copy(__DIR__ . '/version.template.data', self::$versionFilename);
            touch(self::$faviconGifFilename);
            touch(self::$faviconIcoFilename);

            header('Location: /admin/');
        }
    }

    /**
     * @return void
     */
    public static function renderPlan() {

        include __DIR__ . '/snippets/install-plan.php';
    }
}
