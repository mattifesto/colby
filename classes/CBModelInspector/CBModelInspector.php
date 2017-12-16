<?php

final class CBModelInspector {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath() {
        return ['models', 'inspector'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::setTitleHTML('Model Inspector');
        CBHTMLOutput::setDescriptionHTML('View information about a model.');
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
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
    static function CBHTMLOutput_JavaScriptVariables() {
        $ID = cb_query_string_value('ID');
        return [
            ['CBModelInspector_modelID', $ID],
        ];
    }
}
