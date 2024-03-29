<?php

/**
 * @deprecated 2022_10_02_1664717360
 *
 *      This functionaity should be replaced by the CB_Admin_ModelSearch class.
 */
final class
Admin_CBModelList
{
    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'models',
            'directory',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        $modelClassName = cb_query_string_value(
            'modelClassName'
        );

        CBHTMLOutput::pageInformation()->title = (
            "{$modelClassName} Model List"
        );
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v599.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $modelClassName = cb_query_string_value(
            'modelClassName'
        );

        $classHasTemplates = count(
            CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName(
                $modelClassName
            )
        ) > 0;

        return [
            [
                'CBModelsAdmin_classHasTemplates',
                $classHasTemplates,
            ],
            [
                'CBModelsAdmin_modelClassName',
                $modelClassName
            ],
            [
                'CBModelsAdmin_modelList',
                Admin_CBModelList::fetchModelList(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBImage',
            'CBUI',
            'CBUIThumbnailPart',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @NOTE 2022_10_02_1664717109
     *
     *      This function fetches information for every single model with a
     *      given class name. This is bad. CB_Admin_ModelSearch should be
     *      modified to replace this functionality and this clsas should be
     *      removed.
     *
     * @return [object]
     */
    private static function
    fetchModelList(
    ): array
    {
        $modelClassNameAsSQL =
        CBDB::stringToSQL(
            cb_query_string_value(
                'modelClassName'
            )
        );

        $SQL =
        <<<EOT

            SELECT

            LOWER(HEX(CBModelsTable.ID))
            AS ID,

            CBModelsTable.title
            AS title,

            CBModelVersionsTable_images.modelAsJSON
            AS image



            FROM

            CBModels
            AS CBModelsTable



            LEFT JOIN
            CBModelAssociations
            AS CBModelAssociationsTable
            ON
            CBModelsTable.ID = CBModelAssociationsTable.ID AND
            CBModelAssociationsTable.className = "CBModelToCBImageAssociation"



            LEFT JOIN
            CBModels
            AS CBModelsTable_images
            ON
            CBModelAssociationsTable.associatedID = CBModelsTable_images.ID



            LEFT JOIN
            CBModelVersions
            AS CBModelVersionsTable_images
            ON
            CBModelsTable_images.ID = CBModelVersionsTable_images.ID AND
            CBModelsTable_images.version = CBModelVersionsTable_images.version



            WHERE

            CBModelsTable.className =
            {$modelClassNameAsSQL}



            ORDER BY

            CBModelsTable.created DESC

        EOT;

        $modelList =
        CBDB::SQLToObjects(
            $SQL
        );

        foreach(
            $modelList
            as $modelListItem
        ) {
            if (
                $modelListItem->image
                !== null
            ) {
                $modelListItem->image =
                json_decode(
                    $modelListItem->image
                );
            }
        }

        return $modelList;
    }
    /* fetchModelList() */

}
