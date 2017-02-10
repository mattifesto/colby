<?php

final class CBAdminPageForModelInspector {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['test', 'test'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Model Inspector');
        CBHTMLOutput::setDescriptionHTML('View information about a model.');
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [[key, value]]
     */
    static function requiredJavaScriptVariables() {
        $ID = empty($_GET['ID']) ? '' : $_GET['ID'];
        return [
            ['CBAdminPageForModelInspectorID', $ID],
        ];
    }
}
