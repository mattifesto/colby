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
            $editURL = CBSiteURL . "/admin/pages/edit/?data-store-id={$datum->dataStoreID}";
            echo "<div>{$datum->titleHTML} <a href=\"{$editURL}\">edit</a></div>";
        }

        echo '</div>';
    }

    /**
     * @return null
     */
    public static function renderSiteConfigurationIssuesView() {
        if (!ColbyUser::current()->isOneOfThe('Developers')) {
            echo '<!-- CBSiteConfigurationIssuesView -->';
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
        ];

        $messagesAsHTML = [];

        foreach ($deprecatedConstants as $constant) {
            if (defined($constant[0])) {
                $constantAsHTML = cbhtml($constant[0]);
                $message = cbhtml($constant[1]);
                $messagesAsHTML[] = "The `{$constantAsHTML}` constant has been deprecated. {$message}";
            }
        }

        if (empty($messagesAsHTML)) {
            echo '<!-- CBSiteConfigurationIssuesView -->';
            return;
        }

        ?>

        <div class="CBSiteConfigurationIssuesView">
            <h1>Site Configuration Issues</h1>
            <ul>
                <?php array_walk($messagesAsHTML, function($messageAsHTML) {
                    echo "<li>{$messageAsHTML}</li>";
                }); ?>
            </ul>
        </div>

        <?php
    }
}
