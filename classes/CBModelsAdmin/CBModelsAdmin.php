<?php

final class CBModelsAdmin {

    static $page = '';
    static $modelClassName = '';

    /* -- CBAdmin interfaces -- -- -- -- -- */

    /**
     * @return void
     */
    static function CBAdmin_initialize(): void {
        CBModelsAdmin::$page = cb_query_string_value(
            'p',
            'classNameList'
        );

        CBModelsAdmin::$modelClassName = cb_query_string_value(
            'modelClassName'
        );
    }
    /* CBAdmin_initialize() */


    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        switch (CBModelsAdmin::$page) {
            case 'modelList':

                return [
                    'models'
                ];

            default:

                return [
                    'models',
                    'directory',
                ];
        }
    }


    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Models Admin';
    }


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v520.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $classHasTemplates = count(
            CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName(
                CBModelsAdmin::$modelClassName
            )
        ) > 0;

        $variables = [
            [
                'CBModelsAdmin_page',
                CBModelsAdmin::$page,
            ],
            [
                'CBModelsAdmin_classHasTemplates',
                $classHasTemplates,
            ],
        ];

        switch (CBModelsAdmin::$page) {
            case 'modelList':

                $variables[] = [
                    'CBModelsAdmin_modelClassName',
                    cb_query_string_value('modelClassName'),
                ];

                $variables[] = [
                    'CBModelsAdmin_modelList',
                    CBModelsAdmin::fetchModelList(),
                ];

                break;

            default:

                $modelClassNames = CBDB::SQLToArray(
                    'SELECT DISTINCT className FROM CBModels'
                );

                $variables[] = [
                    'CBModelsAdmin_modelClassNames',
                    $modelClassNames,
                ];

                break;
        }

        return $variables;
    }
    /* CBHTMLOutput_JavaScriptVariables() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUINavigationArrowPart',
            'CBUISectionItem4',
            'CBUITitleAndDescriptionPart',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- functions -- -- -- -- -- */

    /**
     * @return [object]
     */
    private static function fetchModelList() {
        $modelClassNameAsSQL = CBDB::stringToSQL(cb_query_string_value('modelClassName'));
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)) AS `ID`, `title`
            FROM    `CBModels`
            WHERE   `className` = {$modelClassNameAsSQL}

EOT;

        return CBDB::SQLToObjects($SQL);
    }
    /* fetchModelList() */
}
