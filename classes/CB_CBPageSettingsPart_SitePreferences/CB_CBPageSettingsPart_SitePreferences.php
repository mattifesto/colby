<?php

final class
CB_CBPageSettingsPart_SitePreferences {

    /* -- CBPageSettings interfaces -- */



    /**
     * @return void
     */
    static function
    CBPageSettings_renderHeadElementHTML(
    ): void {
        $adSensePublisherID = CBSitePreferences::getAdSensePublisherID(
            CBSitePreferences::model()
        );

        if (
            $adSensePublisherID !== ''
        ) {
            $src = (
                'https://pagead2.googlesyndication.com' .
                '/pagead/js/adsbygoogle.js' .
                '?client=' .
                'ca-' .
                $adSensePublisherID
            );

            ?>

            <script
                async
                src="<?= $src ?>"
                crossorigin="anonymous"
            ></script>

            <?php
        }
    }
    /* CBPageSettings_renderHeadElementHTML() */

}
