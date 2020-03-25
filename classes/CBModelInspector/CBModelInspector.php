<?php

final class CBModelInspector {

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
    static function CBAdmin_menuNamePath() {
        return [
            'models',
            'inspector',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Model Inspector';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          ID: hex160
     *      }
     *
     * @return object
     *
     *      {
     *          className: "CBModelInspector_modelData"
     *          versions: [object]
     *      }
     */
    static function CBAjax_fetchModelData(stdClass $args) {
        $ID = CBModel::valueAsID($args, 'ID');

        if ($ID === null) {
            throw CBException::createModelIssueException(
                'The function arguments object has an invalid "ID" ' .
                'property value.',
                $args,
                '334244b01b8d5fa31e4f4371f8d8af2f0a0c8be8'
            );
        }

        $object = (object)[
            'className' => 'CBModelInspector_fetchModelData',

            'associations' => CBModelAssociations::fetch($ID),

            'associatedWith' => CBModelAssociations::fetch(null, null, $ID),

            'modelID' => $ID,
        ];

        $object->dataStoreFiles = CBModelInspector::fetchDataStoreFiles($ID);
        $object->modelVersions = CBModelInspector::fetchModelVersions($ID);
        $object->rowFromCBImages = CBModelInspector::fetchRowFromCBImages($ID);
        $object->rowFromColbyPages = CBModelInspector::fetchRowFromColbyPages($ID);

        return $object;
    }
    /* CBAjax_fetchModelData() */



    /**
     * @return string
     */
    static function CBAjax_fetchModelData_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v592.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $ID = CBConvert::valueAsID(
            cb_query_string_value('ID')
        );

        if ($ID) {
            $associatedImageModel = CBModelAssociations::fetchAssociatedModel(
                $ID,
                'CBModelToCBImageAssociation'
            );
        } else {
            $associatedImageModel = null;
        }

        return [
            [
                'CBModelInspector_modelID',
                $ID,
            ],
            [
                'CBModelInspector_associatedImageModel',
                $associatedImageModel,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBArtworkElement',
            'CBErrorHandler',
            'CBImage',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'CBUIExpander',
            'CBUIImageChooser',
            'CBUINavigationArrowPart',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISectionItem4',
            'CBUIStringEditor',
            'CBUIStringsPart',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @param string $ID
     *
     * @return [object]
     */
    private static function fetchDataStoreFiles(string $ID): array {
        $directory = CBDataStore::directoryForID($ID);
        $files = [];

        if (!is_dir($directory)) {
            return $files;
        }

        $iterator = new RecursiveDirectoryIterator($directory);

        while ($iterator->valid()) {
            if ($iterator->isFile()) {
              $subpathname = $iterator->getSubPathname();
              $URL = CBDataStore::flexpath($ID, $subpathname, cbsiteurl());

              $files[] = (object)[
                  'text' => $subpathname,
                  'URL' => $URL,
              ];
            }

            $iterator->next();
        }

        usort(
            $files,
            function ($file1, $file2) {
                return $file1->text <=> $file2->text;
            }
        );

        return $files;
    }
    /* fetchDataStoreFiles() */



    /**
     * @param string $ID
     *
     * @return [object]
     */
    private static function fetchModelVersions(string $ID): array {
        $IDAsSQL = CBID::toSQL($ID);

        $SQL = <<<EOT

            SELECT      version,
                        timestamp,
                        replaced,
                        specAsJSON,
                        modelAsJSON
            FROM        CBModelVersions
            WHERE       ID = {$IDAsSQL}
            ORDER BY    version DESC

        EOT;

        $versions = CBDB::SQLToObjects($SQL);

        CBModelPruneVersionsTask::assignActions($versions);

        return $versions;
    }



    /**
     * @param CBID $CBID
     *
     * @return object|null
     */
    private static function fetchRowFromCBImages(
        string $CBID
    ): ?stdClass {
        $CBIDAsSQL = CBID::toSQL($CBID);

        $SQL = <<<EOT

            SELECT  created,
                    modified,
                    extension

            FROM    CBImages

            WHERE   ID = {$CBIDAsSQL}

        EOT;

        return CBDB::SQLToObjectNullable($SQL);
    }
    /* fetchRowFromCBImages() */



    /**
     * @param CBID $CBID
     *
     * @return object|null
     */
    private static function fetchRowFromColbyPages(
        string $CBID
    ): ?stdClass {
        $CBIDAsSQL = CBID::toSQL($CBID);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(archiveID)) as archiveID,
                    className,
                    classNameForKind,
                    created,
                    iteration,
                    modified,
                    URI,
                    thumbnailURL,
                    searchText,
                    published,
                    keyValueData

            FROM    ColbyPages

            WHERE   archiveId = {$CBIDAsSQL}

        EOT;

        $result = CBDB::SQLToObjectNullable($SQL);

        if ($result === null) {
            return null;
        } else {
            $result->keyValueData = json_decode($result->keyValueData);

            return $result;
        }
    }
    /* fetchRowFromColbyPages() */

}
