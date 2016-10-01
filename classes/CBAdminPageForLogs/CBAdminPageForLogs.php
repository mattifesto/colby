<?php

final class CBAdminPageForLogs {

    /**
     * @return [string]
     */
    public static function adminPageMenuNamePath() {
        return ['general', 'logs'];
    }

    /**
     * @return stdClass
     */
    public static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Logs');
        CBHTMLOutput::setDescriptionHTML('View logs');
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
