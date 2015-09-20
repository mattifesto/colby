<?php

final class CBAdminPageForGeneral {

    /**
     * @return null
     */
    public static function renderSiteConfigurationIssuesView() {
        if (!ColbyUser::current()->isOneOfThe('Developers')) {
            echo '<!-- CBSiteConfigurationIssuesView -->';
            return;
        }

        $messagesAsHTML = [];

        if (defined('CBShouldDisallowRobots')) {
            $messagesAsHTML[] = <<<EOT

                The CBShouldDisallowRobots constant has been set for this site.
                This constant is deprecated and has been replaced by a site
                preference. Make sure the site preference is set properly and
                then remove this constant.

EOT;
        }

        if (defined('CBGoogleAnalyticsID')) {
            $messagesAsHTML[] = <<<EOT

                The CBGoogleAnalyticsID constant has been set for this site.
                This constant is deprecated and has been replaced by the Google
                Tag Manager ID setting in site preferences. Create a Google Tag
                Manager account for this site and then remove this constant.

EOT;
        }

        if (defined('GOOGLE_UNIVERSAL_ANALYTICS_TRACKING_ID')) {
            $messagesAsHTML[] = <<<EOT

                The GOOGLE_UNIVERSAL_ANALYTICS_TRACKING_ID constant has been set
                for this site. This constant is deprecated and has been replaced
                by the Google Tag Manager ID setting in site preferences. Create
                a Google Tag Manager account for this site and then remove this
                constant.

EOT;
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
