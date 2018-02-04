<?php

final class CBLogAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['general', 'log'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::pageInformation()->title = 'Website Log';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIExpander', 'CBUINavigationView', 'CBUISelector'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v374.js', cbsysurl())];
    }
}
