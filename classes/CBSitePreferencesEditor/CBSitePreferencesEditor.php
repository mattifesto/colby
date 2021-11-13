<?php

final class
CBSitePreferencesEditor {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.41.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        $environmentOptions = array_map(
            function (
                string $environmentOption
            ) {
                return (object)[
                    'title' => $environmentOption,
                    'value' => $environmentOption,
                ];
            },
            CBSitePreferences::getEnvironmentOptions()
        );

        $appearanceOptions = array_map(
            function (
                string $appearanceOption
            ) {
                return (object)[
                    'title' => $appearanceOption,
                    'value' => $appearanceOption,
                ];
            },
            CBSitePreferences::getAppearanceOptions()
        );

        return [
            [
                'CBSitePreferencesEditor_environmentOptions',
                $environmentOptions,
            ],
            [
                'CBSitePreferencesEditor_appearanceOptions',
                $appearanceOptions
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBAjax',
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUIPanel',
            'CBUISelector',
            'CBUISpecArrayEditor',
            'CBUIStringEditor2',

            /**
             * The CBSitePreferences model stores CBKeyValuePair models in the
             * array held by its "custom" property and this class is required to
             * make it available to edit those items even though it is not
             * explicitly used in CBSitePreferencesEditor.js.
             */
            'CBKeyValuePairEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
