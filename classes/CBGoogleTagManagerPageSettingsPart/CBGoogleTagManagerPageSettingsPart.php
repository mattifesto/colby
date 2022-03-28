<?php

final class
CBGoogleTagManagerPageSettingsPart
{
    /* -- CBPageSettings interfaces -- */



    /**
     * @return void
     */
    static function
    CBPageSettings_renderHeadElementHTML(
    ): void
    {

        /**
         * If the site is NOT a development website and the user is an
         * administrator, don't send Google Analytics events for the current
         * page.
         */
        if (
            CBSitePreferences::getIsDevelopmentWebsite() !== true
        ) {
            $currentUserIsAnAdministrator = (
                CBUserGroup::userIsMemberOfUserGroup(
                    ColbyUser::getCurrentUserCBID(),
                    'CBAdministratorsUserGroup'
                )
            );

            if (
                $currentUserIsAnAdministrator
            ) {
                return;
            }
        }

        $googleTagManagerID =
        CBSitePreferences::googleTagManagerID();

        $isUniversalAnalyticsID =
        preg_match(
            '/^UA-/',
            $googleTagManagerID
        );

        if (
            $isUniversalAnalyticsID
        ) {
            $googleGTagScriptURL =
            'https://www.googletagmanager.com/gtag/js?id=' .
            cbhtml(
                $googleTagManagerID
            );

            ?>

            <!-- Global site tag (gtag.js) - Google Analytics -->

            <script
                async
                src="<?= $googleGTagScriptURL ?>"
            >
            </script>

            <script>
              window.dataLayer =
              window.dataLayer ||
              [];

              function gtag()
              {
                  dataLayer.push(
                      arguments
                  );
              }

              gtag(
                  'js',
                  new Date()
              );

              gtag(
                  'config',
                  '<?= $googleTagManagerID ?>'
              );
            </script>

            <?php
        }

        else if (
            $googleTagManagerID !== ''
        ) {
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
    static function
    CBPageSettings_renderPreContentHTML(
    ): void
    {
        $currentUserIsAnAdministrator =
        CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            'CBAdministratorsUserGroup'
        );

        if (
            $currentUserIsAnAdministrator
        ) {
            return;
        }

        $googleTagManagerID =
        CBSitePreferences::googleTagManagerID();

        $isUniversalAnalyticsID =
        preg_match(
            '/^UA-/',
            $googleTagManagerID
        );

        if (
            $isUniversalAnalyticsID
        ) {
            return;
        }

        else if (
            $googleTagManagerID !== ''
        ) {
            $googleTagManagerFrameURL =
            '//www.googletagmanager.com/ns.html?id=' .
            cbhtml(
                $googleTagManagerID
            );

            ?>

            <!-- Google Tag Manager -->

            <noscript>
                <iframe
                    src="<?= $googleTagManagerFrameURL ?>"
                    height="0"
                    width="0"
                    style="display:none;visibility:hidden"
                >
                </iframe>
            </noscript>

            <!-- End Google Tag Manager -->

            <?php
        }
    }
    /* CBPageSettings_renderPreContentHTML() */

}
