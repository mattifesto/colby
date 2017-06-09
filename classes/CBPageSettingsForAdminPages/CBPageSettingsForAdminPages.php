<?php

final class CBPageSettingsForAdminPages {

    /**
     * @param Exception $exception
     *
     * @return null
     */
    static function renderPageForException(Exception $exception) {
         $model = (object)[
             'classNameForSettings' => 'CBPageSettingsForAdminPages',
             'titleHTML' => 'Exception',
             'layout' => (object)[
                 'className' => 'CBPageLayout',
                 'customLayoutClassName' => 'CBAdminPageLayout',
             ],
             'sections' => [
                 (object)[
                     'className' => 'CBExceptionView',
                     'exception' => $exception,
                 ],
             ],
         ];

         CBViewPage::renderModelAsHTML($model);
    }

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
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:700">
        <style>
            html {
                color: hsl(0, 0%, 20%);
                font-family: "Open Sans";
                line-height: 1.4;
            }
        </style>
    <?php }
}
