<?php

final class CBModelInspector {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['models', 'inspector'];
    }

    /**
     * @return object
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
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v359.js', cbsysurl())];
    }

    /**
     * @return [[key, value]]
     */
    static function requiredJavaScriptVariables() {
        $ID = cb_query_string_value('ID');
        return [
            ['CBModelInspector_modelID', $ID],
        ];
    }
}
