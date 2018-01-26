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
    static function renderPageForException(Throwable $exception) {
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
    static function CBHTMLOutput_renderHeadContent() { ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
                --CBBackgroundColor2: hsl(0, 0%, 95%);
                --CBBackgroundColorForPanel: hsla(0, 0%, 100%, 0.8);
                --CBLineColor: hsla(0, 0%, 0%, 0.2);
                --CBTextColor: hsla(0, 0%, 0%, 0.9);
                --CBTextColor2: hsla(0, 0%, 0%, 0.6);
                --CBTextColorForHeadings: hsla(0, 0%, 0%, 0.9);
                --CBTextColorForLinks: hsla(210, 80%, 50%, 0.9);

                background-color: var(--CBBackgroundColor);
                color: var(--CBTextColor);
            }
        </style>
    <?php }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredHeadClassNames() {
        return ['CBEqualize'];
    }
}
