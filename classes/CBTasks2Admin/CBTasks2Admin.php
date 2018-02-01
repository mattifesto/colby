<?php

final class CBTasks2Admin {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['general', 'tasks'];
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
        CBHTMLOutput::setTitleHTML('Tasks');
        CBHTMLOutput::setDescriptionHTML('View tasks');
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v378.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUISectionItem4', 'CBUIStringsPart'];
    }
}
