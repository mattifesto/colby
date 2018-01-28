<?php

final class CBPageSettingsForAdminPages {

    /**
     * @return string
     */
    static function defaultThemeClassName() {
        return 'CBLightTheme';
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
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:600">
        <style>
            html {
                font-family: "Open Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
                font-size: 16px;
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
