<?php

define('CBSiteDirectory', $_SERVER['DOCUMENT_ROOT']);

ColbyInstaller::initialize();

class ColbyInstaller {

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
    public static function initialize() {

        self::$dataDirectory                = CBSiteDirectory . '/data';
        self::$tmpDirectory                 = CBSiteDirectory . '/tmp';

        self::$colbyConfigurationFilename   = CBSiteDirectory . '/colby-configuration.php';
        self::$colbyFilename                = CBSiteDirectory . '/colby.php';
        self::$faviconGifFilename           = CBSiteDirectory . '/favicon.gif';
        self::$faviconIcoFilename           = CBSiteDirectory . '/favicon.ico';
        self::$gitignoreFilename            = CBSiteDirectory . '/.gitignore';
        self::$HTAccessFilename             = CBSiteDirectory . '/.htaccess';
        self::$indexFilename                = CBSiteDirectory . '/index.php';
        self::$siteConfigurationFilename    = CBSiteDirectory . '/site-configuration.php';
        self::$versionFilename              = CBSiteDirectory . '/version.php';


        if (isset($_GET['install']) && $_GET['install'] == 'true') {

            self::install();

        } else {

            self::renderPlan();
        }
    }

    /**
     * @return void
     */
    public static function install() {

        if (!is_dir(self::$dataDirectory)) {

            mkdir(self::$dataDirectory);
        }

        if (!is_dir(self::$tmpDirectory)) {

            mkdir(self::$tmpDirectory);
        }

        if (!is_file(self::$colbyConfigurationFilename)) {

            copy(__DIR__ . '/colby-configuration.template.data', self::$colbyConfigurationFilename);
        }

        if (!is_file(self::$colbyConfigurationFilename)) {

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

        header('Location: /admin/');
    }

    /**
     * @return void
     */
    public static function renderPlan() {

        include __DIR__ . '/snippets/install-plan.php';
    }

    /**
     * This function determines whether a full or partial installation should
     * occur. In the future this function may do more detailed analysis to
     * determine the correct course of action.
     *
     * For now, if the `index.php` file exists, this will be a partial
     * installation. Otherwise it will be full installation.
     *
     * @return Boolean
     */
    private static function shouldPerformAFullInstallation() {

        return !is_file(self::$indexFilename);
    }
}
