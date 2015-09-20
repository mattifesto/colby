<?php

final class CBPageSettingsForResponsivePages {

    /**
     * @return  null
     */
    public static function renderEndOfBodyContent() { ?>
        <script src="<?= CBSystemURL ?>/javascript/Colby.js"></script>
    <?php }

    /**
     * @return  null
     */
    public static function renderHeadContent() { ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="<?= CBSystemURL ?>/javascript/html5shiv.js"></script>
        <script src="<?= CBSystemURL ?>/javascript/ColbyEqualize.js"></script>
        <link rel="stylesheet" href="<?= CBSystemURL ?>/css/equalize.css">
    <?php }

    /**
     * @return  null
     */
    public static function renderStartOfBodyContent() {
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
}
