<?php

/**
 * This class is used to help render the /admin/ page.
 */
final class CBStatusAdminPage {

    /**
     * @param string $pageStub
     *
     * @return [string]
     */
    static function CBAdmin_menuNamePath(string $pageStub) {
        return ['general', 'status'];
    }

    /**
     * @param string $pageStub
     *
     * @return void
     */
    static function CBAdmin_render($pageStub) {
        CBHTMLOutput::setTitleHTML('Website Status');
        CBHTMLOutput::setDescriptionHTML('The status of the website');

        ?>

        <main class="CBUIRoot">
            <div class="CBLibraryListView">
                <?php

                $statusWidgetFilepaths = Colby::globFiles('classes/CBStatusWidgetFor*');
                $statusWidgetClassNames = array_map(function ($filepath) {
                    return basename($filepath, '.php');
                }, $statusWidgetFilepaths);

                sort($statusWidgetClassNames);

                array_walk($statusWidgetClassNames, function($className) {
                    if (is_callable($function = "{$className}::CBStatusAdminPage_data")) {
                        $data = call_user_func($function);

                        ?>

                        <section class="widget">
                            <header><h1><?= cbhtml($data[0]) ?></h1></header>

                            <div class="version-numbers">
                                <section class="version-number">
                                    <h1><?= cbhtml($data[1]) ?></h1>
                                    <div class="number"><?= cbhtml($data[2]) ?></div>
                                </section>
                            </div>
                        </section>

                        <?php
                    }
                });

                /* deprecated: use status widget classes */
                $widgetClassNames = Colby::globFiles('classes/*AdminWidgetFor*');
                $widgetClassNames = array_map(function ($className) {
                    return basename($className, '.php');
                }, $widgetClassNames);

                sort($widgetClassNames);

                array_walk($widgetClassNames, function($className) {
                    if (is_callable($function = "{$className}::render")) {
                        call_user_func($function);
                    }
                });

                /* deprecated: use status widget classes */
                $adminWidgetFilenames = Colby::globFiles('snippets/admin-widget-*.php');

                foreach ($adminWidgetFilenames as $adminWidgetFilename) {
                    include $adminWidgetFilename;
                }

                ?>
            </div>

            <?php

            CBStatusAdminPage::renderDuplicatePublishedURIWarnings();
            CBStatusAdminPage::renderSiteConfigurationIssuesView();
            CBStatusAdminPage::renderPingStatus();

            ?>

        </main>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v358.css', cbsysurl())];
    }

    /**
     * @return null
     */
    static function renderDuplicatePublishedURIWarnings() {
        $SQL = <<<EOT

            SELECT      `URI`
            FROM        `ColbyPages`
            WHERE       `published` IS NOT NULL AND
                        `URI` IS NOT NULL
            GROUP BY    `URI`
            HAVING      COUNT(*) > 1

EOT;

        $URIs = CBDB::SQLToArray($SQL);

        if (!empty($URIs)) {
            ?>

            <div class="CBUIHalfSpace"></div>
            <h1 class="CBUISectionHeader">Duplicate URI Warnings</h1>
            <div class="CBUISection">

                <?php

                foreach ($URIs as $URI) {
                    CBStatusAdminPage::renderDuplicatePublishedURIWarningForURI($URI);
                }

                ?>

            </div>

            <?php

            CBUI::renderHalfSpace();
        }
    }

    /**
     * @return null
     */
    static function renderDuplicatePublishedURIWarningForURI($URI) {
        $URIAsSQL = CBDB::stringToSQL($URI);
        $SQL = <<<EOT

            SELECT      `keyValueData`
            FROM        `ColbyPages`
            WHERE       `URI` = {$URIAsSQL} AND
                        `published` IS NOT NULL
            ORDER BY    `published` ASC

EOT;

        $data = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

        echo '<div class="CBUISectionItem CBURIItem">';
        echo '<div class="URI">URI: ', ColbyConvert::textToHTML($data[0]->URI), '</div>';

        foreach ($data as $datum) {
            $editURL = CBSitePreferences::siteURL() . "/admin/pages/edit/?data-store-id={$datum->dataStoreID}";
            echo "<div>{$datum->titleHTML} <a href=\"{$editURL}\">edit</a></div>";
        }

        echo '</div>';
    }

    /**
     * @return null
     */
    static function renderPingStatus() {
        $now = time();
        $spec = CBModels::fetchSpecByID(CBRemoteAdministration::pingModelID());

        CBUI::renderSectionStart();

        if ($spec === false) {
            $message = "Alert: This site has never been pinged.";
        } else {
            $pinged = CBModel::value($spec, 'pinged', 0, 'intval');

            if ($pinged < ($now - (60 * 15))) {
                $message = "Alert: This site has not been pinged for over 15 minutes.";
            } else {
                $message = "Healthy";
            }
        }

        CBUI::renderKeyValueSectionItem('Ping Status', $message);
        CBUI::renderSectionEnd();
        CBUI::renderHalfSpace();
    }

    /**
     * @return null
     */
    static function renderSiteConfigurationIssuesView() {
        echo '<!-- Site Configuration Issues -->';

        if (!ColbyUser::current()->isOneOfThe('Developers')) {
            return;
        }

        /**
         * Deprecated constant strings are broken up to avoid false positives
         * when searching for them with tools.
         */
        $deprecatedConstants = [
            ['CB'.'SiteIsBeingDebugged', 'Use site preferences.'],
            ['CB'.'SiteIsBeingDubugged', 'Use site preferences.'],
            ['COLBY'.'_SITE_IS_BEING_DEBUGGED', 'Use site preferences.'],
            ['CB'.'ShouldDisallowRobots', 'Use site preferences.'],
            ['CB'.'GoogleAnalyticsID', 'Use site preferences'],
            ['GOOGLE'.'_UNIVERSAL_ANALYTICS_TRACKING_ID', 'Use site preferences'],
            ['CB'.'SiteConfiguration::defaultClassNameForPageSettings', 'Use site preferences.'],
            ['CB'.'FacebookFirstVerifiedUserID', 'Remove it.'],
            ['COLBY'.'_FACEBOOK_FIRST_VERIFIED_USER_ID', 'Remove it.'],
            ['COLBY'.'_SITE_ERRORS_SEND_EMAILS', 'Use CBSiteDoesSendEmailErrorReports instead.'],
            ['COLBY'.'_SITE_NAME', 'Remove it and use site preferences.'], // 2017.03.17
            ['CB'.'SiteName', 'Remove it and use site preferences.'], // 2017.03.17
            ['CB'.'SiteNameHTML', 'Remove it and use site preferences.'], // 2017.03.17
            ['COLBY'.'_SITE_URL', 'Remove it and use site preferences.'], // 2017.03.19
            ['CB'.'SiteAdministratorEmail', 'Remove it and use site preferences.'], // 2017.03.22
            ['COLBY'.'_SITE_ADMINISTRATOR', 'Remove it and use site preferences.'], // 2017.03.22
            ['COLBY_FACEBOOK_APP_ID', 'Use CBFacebookAppID instead.'], // 2017.07.08
            ['COLBY_FACEBOOK_APP_SECRET', 'Use CBFacebookAppSecret instead.'], // 2017.07.08
            ['COLBY_MYSQL_HOST', 'Use CBMySQLHost instead.'], // 2017.07.08
            ['COLBY_MYSQL_USER', 'Use CBMySQLUser instead.'], // 2017.07.08
            ['COLBY_MYSQL_PASSWORD', 'Use CBMySQLPassword instead.'], // 2017.07.08
            ['COLBY_MYSQL_DATABASE', 'Use CBMySQLDatabase instead.'], // 2017.07.08
        ];

        $hasContent = false;

        ob_start();

        foreach ($deprecatedConstants as $constant) {
            if (defined($constant[0])) {
                CBUI::renderKeyValueSectionItem($constant[0], 'This constant has been deprecated. ' . $constant[1]);
            }
        }

        /* 2017.07.15 The classNamesForKinds property on the CBPagesPreferences
           model has beed deprecated. */
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $kinds = CBModel::value($model, 'classNamesForKinds', []);

        if (!empty($kinds)) {
            CBUI::renderKeyValueSectionItem('CBPagesPreferences::classNamesForKinds', 'Implement CBPageHelpers::classNamesForPageKinds() instead of the CBPagesPreferences model.');
        }

        /* 2017.07.17 The classNameForPageSettings property on the
           CBSitePreferences model has been deprecated. */
        $model = CBSitePreferences::model();
        $className = CBModel::value($model, 'defaultClassNameForPageSettings');

        if (!empty($className)) {
            CBUI::renderKeyValueSectionItem('CBSitePreferences::classNameForPageSettings', 'Implement CBPageHelpers::classNameForUnsetPageSettings() instead of the CBSitePreferences model.');
        }

        /* 2017.08.17 The slackWebhookURL propery on the CBSitePreferences model
           is not technically required, but it is effectively required because
           an admin should be tracking site errors. */
        $slackWebhookURL = CBModel::value($model, 'slackWebhookURL');

        if (empty($slackWebhookURL)) {
            CBUI::renderKeyValueSectionItem('Slack Webhook URL', 'Enter a Slack Webhook URL in site preferences to receive error notifications.');
        }

        /* 2017.08.17 Search for use of deprecated `colby/Colby.php` file. */
        if (is_file($filepath = cbsitedir() . '/index.php')) {
            $text = file_get_contents($filepath);

            if (preg_match('/Colby\.php/', $text)) {
                CBUI::renderKeyValueSectionItem('colby/Colby.php', 'The "index.php" file for this site references the deprecated "colby/Colby.php" file. Replace this with a reference to the "colby/init.php" file.');
            }
        }

        /* 2017.11.20 Warn if the site .htaccess is different from the Colby
          .htaccess */
        if (sha1_file(cbsitedir() . '/.htaccess') !== sha1_file(cbsysdir() . '/setup/htaccess.template.data')) {
            CBUI::renderKeyValueSectionItem('.htaccess', 'The .htaccess file is different from the suggested Colby .htaccess file.');
        }

        $content = ob_get_clean();

        if (!empty($content)) {
            CBUI::renderSectionHeader('Site Configuration Issues');
            CBUI::renderSectionStart();

            echo $content;

            CBUI::renderSectionEnd();
            CBUI::renderHalfSpace();
        }
    }
}
