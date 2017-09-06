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
    * @return  {
    *              category: string
    *              message: string
    *              model: object
    *              severity: int
    *              timestamp: int
    *          }
     */
    static function CBAjax_fetchEntries() {
        return CBLog::entries((object)[
            'sinceTimestamp' => time() - (60 * 60 * 24 * 7), // 7 days
        ]);
    }

    /**
     * @return string
     */
    static function CBAjax_fetchEntries_group() {
        return 'Administrators';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUIExpander'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }
}
