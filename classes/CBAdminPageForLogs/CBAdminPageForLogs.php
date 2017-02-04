<?php

final class CBAdminPageForLogs {

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
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
