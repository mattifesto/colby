<?php

final class CBAdminPageForTasks {

    /**
     * @return [string]
     */
    public static function adminPageMenuNamePath() {
        return ['general', 'tasks'];
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
        CBHTMLOutput::setTitleHTML('Tasks');
        CBHTMLOutput::setDescriptionHTML('View tasks');
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
