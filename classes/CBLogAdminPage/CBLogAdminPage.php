<?php

final class CBLogAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['general', 'logs'];
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
        CBHTMLOutput::setTitleHTML('Logs');
        CBHTMLOutput::setDescriptionHTML('View logs');
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
        return [Colby::flexpath(__CLASS__, 'v364.js', cbsysurl())];
    }
}
