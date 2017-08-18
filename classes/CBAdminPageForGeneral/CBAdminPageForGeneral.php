<?php

final class CBAdminPageForGeneral {

    /**
     * @return null
     */
    public static function renderDuplicatePublishedURIWarnings() {
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
                    CBAdminPageForGeneral::renderDuplicatePublishedURIWarningForURI($URI);
                }

                ?>

            </div>
            <div class="CBUIHalfSpace"></div>

            <?php
        }
    }

    /**
     * @return null
     */
    public static function renderDuplicatePublishedURIWarningForURI($URI) {
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

        CBUI::renderHalfSpace();
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

        if (is_dir(cbsitedir() . '/swiftmailer')) {
            CBUI::renderKeyValueSectionItem('Swiftmailer', 'Your site directory contains a `swiftmailer` directory. Swiftmailer use has been deprecated.');
        }

        /* 2017.08.17 Search for use of deprecated `colby/Colby.php` file. */
        if (is_file($filepath = cbsitedir() . '/index.php')) {
            $text = file_get_contents($filepath);

            if (preg_match('/Colby\.php/', $text)) {
                CBUI::renderKeyValueSectionItem('colby/Colby.php', 'The "index.php" file for this site references the deprecated "colby/Colby.php" file. Replace this with a reference to the "colby/init.php" file.');
            }
        }

        $content = ob_get_clean();

        if (!empty($content)) {
            CBUI::renderHalfSpace();
            CBUI::renderSectionHeader('Site Configuration Issues');
            CBUI::renderSectionStart();

            echo $content;

            CBUI::renderSectionEnd();
        }
    }
}
