<?php

final class SCPreferencesEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v662.js', scliburl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $preferencesModel = CBModelCache::fetchModelByID(
            SCPreferences::ID()
        );

        $defaultOrderKindClassName = CBModel::valueToString(
            $preferencesModel,
            'defaultOrderKindClassName'
        );

        return [
            [
                'SCPreferencesEditor_defaultOrderKindClassName',
                $defaultOrderKindClassName,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBException',
            'CBModel',
            'CBUI',
            'CBUIPanel',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */
}
