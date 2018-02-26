<?php

/**
 * @deprecated 2018.02.26
 *
 *      This class no longer creates static width pages. This class will be
 *      removed as soon as removal is known to not cause issues.
 */
final class CBPageSettingsForStaticPages {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_htmlClassNames(): array {
        return ['CBLightTheme', 'CBStyleSheet'];
    }

    /**
     * @return void
     */
    static function CBHTMLOutput_renderHeadContent(): void {
        ?>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
    }

    /**
     * @return void
     */
    static function renderStartOfBodyContent(): void {
        $googleTagManagerID = CBSitePreferences::googleTagManagerID();

        if ($googleTagManagerID !== '') { ?>
            <!-- Google Tag Manager -->
            <noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?= $googleTagManagerID ?>"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?= $googleTagManagerID ?>');</script>
            <!-- End Google Tag Manager -->
        <?php }
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredHeadClassNames(): array {
        return ['CBEqualize'];
    }
}
