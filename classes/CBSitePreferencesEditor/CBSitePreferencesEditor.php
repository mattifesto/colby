<?php

final class CBSitePreferencesEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v486.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBImage',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUISpecArrayEditor',
            'CBUIStringEditor',
            'Colby',

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
