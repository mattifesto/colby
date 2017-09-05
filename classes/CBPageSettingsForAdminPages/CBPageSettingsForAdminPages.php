<?php

final class CBPageSettingsForAdminPages {

    /**
     * @return string
     */
    static function defaultThemeClassName() {
        return 'CBAdminTheme';
    }

    /**
     * @param Throwable $exception
     *
     * @return null
     */
    static function renderPageForException(/* Throwable */ $exception) {
         $model = (object)[
             'className' => 'CBViewPage',
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

         CBPage::render($model);
    }

    /**
     * @return  null
     */
    static function renderEndOfBodyContent() {
    }

    /**
     * @return  null
     */
    static function renderHeadContent() { ?>
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
            .CBAdminTheme {
                --CBBackgroundColor: hsl(0, 0%, 100%);
                --CBBackgroundColor2: hsl(0, 0%, 90%);
                --CBBackgroundColorForPanel: hsla(0, 0%, 100%, 0.8);
                --CBLineColor: hsla(0, 0%, 0%, 0.2);
                --CBTextColor: hsla(0, 0%, 0%, 0.8);
                --CBTextColor2: hsla(0, 0%, 0%, 0.6);
                --CBTextColorForHeadings: hsla(0, 0%, 0%, 1.0);

                background-color: var(--CBBackgroundColor);
                color: var(--CBTextColor);
            }
        </style>
    <?php }
}
