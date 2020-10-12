<?php

final class CBGoogleTagManagerPageSettingsPart {

    /* -- CBPageSettings interfaces -- */



    /**
     * @return void
     */
    static function CBPageSettings_renderHeadElementHTML(
    ): void {
        $currentUserIsAnAdministrator = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        if ($currentUserIsAnAdministrator) {
            return;
        }

        $googleTagManagerID = CBSitePreferences::googleTagManagerID();

        $isUniversalAnalyticsID = preg_match(
            '/^UA-/',
            $googleTagManagerID
        );

        if ($isUniversalAnalyticsID) {
            ?>

            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $googleTagManagerID ?>"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', '<?= $googleTagManagerID ?>');
            </script>

            <?php
        }

        else if ($googleTagManagerID !== '') {
            ?>

            <!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?= $googleTagManagerID ?>');</script>
            <!-- End Google Tag Manager -->

            <?php
        }
    }
    /* CBPageSettings_renderHeadElementHTML() */



    /**
     * @return void
     */
    static function CBPageSettings_renderPreContentHTML(
    ): void {
        $currentUserIsAnAdministrator = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        if ($currentUserIsAnAdministrator) {
            return;
        }

        $googleTagManagerID = CBSitePreferences::googleTagManagerID();

        $isUniversalAnalyticsID = preg_match(
            '/^UA-/',
            $googleTagManagerID
        );

        if ($isUniversalAnalyticsID) {
            return;
        }

        else if ($googleTagManagerID !== '') {
            ?>

            <!-- Google Tag Manager -->
            <noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?= $googleTagManagerID ?>"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager -->

            <?php
        }
    }
    /* CBPageSettings_renderPreContentHTML() */

}
