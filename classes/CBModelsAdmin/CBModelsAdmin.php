<?php

final class CBModelsAdmin {

    static $page = '';
    static $modelClassName = '';



    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



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
            Colby::flexpath(__CLASS__, 'v544.js', cbsysurl()),
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
            'CBImage',
            'CBUI',
            'CBUINavigationArrowPart',
            'CBUISectionItem4',
            'CBUITitleAndDescriptionPart',
            'CBUIThumbnailPart',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [object]
     */
    private static function fetchModelList() {
        $modelClassNameAsSQL = CBDB::stringToSQL(
            cb_query_string_value('modelClassName')
        );

        $SQL = <<<EOT

            SELECT      LOWER(HEX(CBModelsTable.ID)) AS ID,
                        CBModelsTable.title AS title,
                        CBModelVersionsTable_images.modelAsJSON AS image

            FROM        CBModels as CBModelsTable

            LEFT JOIN   CBModelAssociations AS CBModelAssociationsTable
                ON      CBModelsTable.ID = CBModelAssociationsTable.ID AND
                        CBModelAssociationsTable.className = "CBModelToCBImageAssociation"

            LEFT JOIN   CBModels as CBModelsTable_images
                ON      CBModelAssociationsTable.associatedID = CBModelsTable_images.ID

            LEFT JOIN   CBModelVersions as CBModelVersionsTable_images
                ON      CBModelsTable_images.ID = CBModelVersionsTable_images.ID AND
                        CBModelsTable_images.version = CBModelVersionsTable_images.version

            WHERE       CBModelsTable.className = {$modelClassNameAsSQL}

            ORDER BY    CBModelsTable.created DESC

        EOT;

        $modelList = CBDB::SQLToObjects($SQL);

        foreach($modelList as $modelListItem) {
            if ($modelListItem->image !== null) {
                $modelListItem->image = json_decode($modelListItem->image);
            }
        }

        return $modelList;
    }
    /* fetchModelList() */

}
