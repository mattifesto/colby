<?php

/**
 * This class tries to be as simple as possible. Here are its responsibilities:
 *
 * - Provide data storage for specs and models
 * - Provide data storage for historical versions of specs and models
 * - Keep track of the the version that is most recent
 * - Provide an API to ensure data integrity
 * - Provide an API to give best possible performance for multiple operations
 */
final class
CBModels {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * This function is a heavy-duty delete. It will remove the model, and it
     * will also remove the data store. If you need the files in the data store
     * you should not be deleting the model.
     *
     * @param object $args
     *
     *      {
     *          ID: string
     *      }
     *
     * @return null
     */
    static function CBAjax_deleteByID(
        stdClass $args
    ) {
        $ID = CBModel::value(
            $args,
            'ID'
        );

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID($ID);
            }
        );
    }
    /* CBAjax_deleteByID() */



    /**
     * @return string
     */
    static function CBAjax_deleteByID_getUserGroupClassName() {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return void
     */
    static function CBAjax_revert(
        stdClass $args
    ): void {
        $ID = CBModel::valueAsID($args, 'ID');

        if ($ID === null) {
            throw CBException::createModelIssueException(
                'The "ID" property is not a valid ID.',
                $args,
                '4fa9a03d08fe03aaac74891718fb02f247ede2ca'
            );
        }

        $version = CBModel::valueAsInt($args, 'version');

        if ($version === null) {
            throw CBException::createModelIssueException(
                'The "version" property is not a valid integer.',
                $args,
                'f37d2fdd918adcedce86682d0499b7001f65f6b2'
            );
        }

        CBModels::revert($ID, $version);
    }
    /* CBAjax_revert() */



    /**
     * @return string
     */
    static function CBAjax_revert_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * This function is used often by websites to save models. The page editor
     * and model editors use it.
     *
     * Testing:
     *
     *      Because this function is so easy to execute by editing a page or
     *      other model, it can be used to test Colby Ajax functionaily. Placing
     *      breakpoints in JavaScript while altering this function can simulate
     *      various scenarios.
     *
     *      1. You can uncomment the code at the beginning of the function to
     *         test the handling various HTTP status codes.
     *      2. You can stop your development web server to test the handling of
     *         server unavailable errors.
     *
     * @return void
     */
    static function CBAjax_save($args): void {

        /**
         * Uncomment to test failure of this Ajax function:
         *
         * header("HTTP/1.0 404 Not Found"); return;
         */

        $spec = CBModel::valueAsModel($args, 'spec');

        if ($spec === null) {
            throw CBException::createModelIssueException(
                'The "spec" property of the arguments is not a valid model.',
                $args,
                'c6490ee97e815aa76e6821920922406ec764d10f'
            );
        }

        $ID = CBModel::valueAsID($spec, 'ID');

        if ($ID === null) {
            throw CBException::createModelIssueException(
                'The spec to save does not have a valid ID',
                $spec,
                '666e8ff4a6eac1b5df92a3708eedfd0c745fbbae'
            );
        }

        if (!CBModels::currentUserCanWrite($spec)) {
            throw CBException::createModelIssueException(
                'The current user does not have the permissions to save ' .
                'this spec.',
                $spec,
                'f415157340b172d67f4c2fc7afa75888cdb4b72e'
            );
        }

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }
    /* CBAjax_save() */



    /**
     * @return string
     */
    static function CBAjax_save_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v479.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBException',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * When a model is saved a task is created, so CBTasks2 is required for this
     * class to be fully installed.
     *
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModelsTable',
            'CBModelVersionsTable',
            'CBTasks2',
            'CBUpgradesForVersion442',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param stdClass $model
     *
     * @return bool
     *
     *      Returns true if the current user can write the model via Ajax;
     *      otherwise false. If permissions aren't implement by the model class
     *      the default is that Administrators can write and others can't.
     */
    static function currentUserCanWrite(stdClass $model) {
        if (empty($model->className)) {
            return false;
        }

        if (
            is_callable(
                $function = "{$model->className}::CBModels_currentUserCanWrite"
            )
        ) {
            return call_user_func($function, $model);
        } else if (
            is_callable(
                $function = "{$model->className}::currentUserCanWrite"
            )
        ) {
            return call_user_func($function, $model);
        } else {
            return CBUserGroup::currentUserIsMemberOfUserGroup(
                'CBAdministratorsUserGroup'
            );
        }
    }
    /* currentUserCanWrite() */



    /**
     * Delete models. This function will also delete the data stores associated
     * with the models it deletes. If a data store exists, a model should also
     * exists to represent it.
     *
     * Important: This function executes multiple queries each of which must
     * succeed for the save to be successful, so it should always be called
     * inside of a transaction.
     *
     *      Colby::query('START TRANSACTION');
     *      CBModels::deleteByID($ID);
     *      Colby::query('COMMIT');
     *
     * @param CBID|[CBID] $IDs
     *
     *      All of the referenced models must have the same class name. Make
     *      separate calls for each class name.
     *
     * @return void
     */
    static function deleteByID(
        $IDs
    ): void {
        if (empty($IDs)) {
            return;
        }

        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        $IDsForSQL = CBID::toSQL($IDs);

        $SQL = <<<EOT

            SELECT DISTINCT className

            FROM            CBModels

            WHERE           ID IN ({$IDsForSQL})

        EOT;

        $classNames = CBDB::SQLtoArray($SQL);

        if (count($classNames) > 0) {
            if (count($classNames) > 1) {
                $classNames = implode(', ', $classNames);
                $method = __METHOD__;

                throw new RuntimeException(
                    "The IDs provided to {$method} have multiple class " .
                    "names: {$classNames}."
                );
            }

            $className = $classNames[0];
            $functionName = "{$className}::CBModels_willDelete";

            if (is_callable($functionName)) {
                call_user_func(
                    $functionName,
                    $IDs
                );
            } else {
                $functionName = "{$className}::modelsWillDelete";

                if (is_callable($functionName)) {
                    call_user_func(
                        $functionName,
                        $IDs
                    );
                }
            }
        }

        $SQL = <<<EOT

            DELETE  CBModels,
                    CBModelVersions

            FROM    CBModels

            JOIN    CBModelVersions
            ON      CBModelVersions.ID = CBModels.ID

            WHERE   CBModels.ID IN ({$IDsForSQL})

        EOT;

        Colby::query($SQL);

        foreach ($IDs as $ID) {
            CBDataStore::deleteByID($ID);
        }

        /**
         * If any of the models being deleted are in the cache, remove them now.
         */
        if (class_exists('CBModelCache', false)) {
            CBModelCache::uncacheByID($IDs);
        }
    }
    /* deleteByID() */



    /**
     * There can be multiple CBIDs associated with a URLPath. This is not a
     * great situation but it is possible. This function always selects the CBID
     * that was created first.
     *
     * @param string $URLPath
     *
     * @return CBID|null
     */
    static function
    fetchCBIDByURLPath(
        $URLPath
    ): ?string {
        $URLPathAsSQL = CBDB::stringToSQL(
            $URLPath
        );

        $SQL = <<<EOT

            SELECT
            LOWER(HEX(CBModels2_CBID_column))

            FROM
            CBModels2_table

            WHERE
            CBModels2_URLPath_column = {$URLPathAsSQL}

            ORDER BY
            CBModels2_created_column

            LIMIT 1

        EOT;

        /**
         * @TODO 2021_12_04
         *
         *      Remove try/catch block in Colby version 676
         */

        try {
            $CBID = CBDB::SQLToValue2(
                $SQL
            );
        } catch (
            Throwable $throwable
        ) {
            if (
                Colby::mysqli()->errno === 1146
            ) {
                error_log(
                    'CBModels2_table does not yet exist. Update website.'
                );

                return null;
            } else {
                throw $throwable;
            }
        }

        return $CBID;
    }
    /* fetchCBIDByURLPath() */



    /**
     * @param string $className
     *
     * @return [CBID]
     */
    static function
    fetchCBIDsByClassName(
        string $className
    ): array {
        $classNameAsSQL = CBDB::stringToSQL(
            $className
        );

        $SQL = <<<EOT

            SELECT  LOWER(HEX(ID))
            FROM    CBModels
            WHERE   className = {$classNameAsSQL}

        EOT;

        return CBDB::SQLToArrayOfNullableStrings(
            $SQL
        );
    }
    /* fetchCBIDsByClassName() */



    /**
     * @param [string] $IDs
     *
     * @return [int]
     */
    private static function fetchCreatedTimestampsForIDs(
        array $IDs
    ) {
        if (empty($IDs)) {
            return [];
        }

        $IDsAsSQL = CBID::toSQL($IDs);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)),
                    `timestamp`

            FROM    `CBModelVersions`

            WHERE   `ID` in ({$IDsAsSQL}) AND
                    `version` = 0

        EOT;

        return CBDB::SQLtoArray($SQL);
    }
    /* fetchCreatedTimestampsForIDs() */



    /**
     * @param string $CBID
     *
     * @return object|null
     */
    static function
    fetchModelByCBID(
        string $CBID
    ): ?stdClass {
        $models = CBModels::fetchModelsByID(
            [$CBID]
        );

        if (empty($models)) {
            return null;
        } else {
            return $models[$CBID];
        }
    }
    /* fetchModelByCBID() */



    /**
     * @deprecated use fetchModelByCBID()
     *
     * @param ID $ID
     *
     * @return model|false
     */
    static function fetchModelByID($ID) {
        $models = CBModels::fetchModelsByID([$ID]);

        if (empty($models)) {
            return false;
        } else {
            return $models[$ID];
        }
    }
    /* fetchModelByID() */



    /**
     * @deprecated use fetchModelByCBID()
     *
     * @param ID $ID
     *
     * @return ?model
     */
    static function fetchModelByIDNullable(string $ID): ?stdClass {
        return CBModels::fetchModelByCBID(
            $ID
        );
    }
    /* fetchModelByIDNullable() */



    /**
     * @deprecated use CBModels::fetchModelsByClassName2()
     *
     * @param string $className
     *
     * @return [ID => model]
     */
    static function fetchModelsByClassName(
        string $className
    ): array {
        $classNameAsSQL = CBDB::stringToSQL($className);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(m.ID)),
                    v.modelAsJSON

            FROM    CBModels as m

            JOIN    CBModelVersions as v ON
                    m.ID = v.ID AND
                    m.version = v.version

            WHERE   m.className = {$classNameAsSQL}

        EOT;

        return CBDB::SQLToArray(
            $SQL,
            [
                'valueIsJSON' => true,
            ]
        );
    }
    /* fetchModelsByClassName() */



    /**
     * This function will return all the models with a given class name. This
     * function should be used mindfully because there are some class names with
     * a high number of models. This function is intended to be used in cases
     * where the caller is aware that the total number of models that will be
     * returned is likely to be reasonable.
     *
     * @param string $className
     *
     * @return [object]
     */
    static function fetchModelsByClassName2(
        string $className
    ): array {
        $classNameAsSQL = CBDB::stringToSQL($className);

        $SQL = <<<EOT

            SELECT  v.modelAsJSON

            FROM    CBModels as m

            JOIN    CBModelVersions as v ON
                    m.ID = v.ID AND
                    m.version = v.version

            WHERE   m.className = {$classNameAsSQL}

        EOT;

        $valuesAsJSON = CBDB::SQLToArrayOfNullableStrings($SQL);

        return array_map(
            function ($JSON) {
                return CBConvert::JSONToValue($JSON);
            },
            $valuesAsJSON
        );
    }
    /* fetchModelsByClassName2() */



    /**
     * @deprecated use fetchModelsByID2()
     *
     * @param [ID] $IDs
     *
     * @return [ID => model]
     *
     *      If no model exists for an ID there will be no item in the returned
     *      array for that ID.
     */
    static function fetchModelsByID(array $IDs): array {
        if (empty($IDs)) {
            return [];
        }

        $IDsAsSQL = CBID::toSQL($IDs);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(m.ID)),
                    v.modelAsJSON

            FROM    CBModels AS m

            JOIN    CBModelVersions AS v
                ON  m.ID = v.ID AND
                    m.version = v.version

            WHERE   m.ID IN ($IDsAsSQL)

        EOT;

        return CBDB::SQLToArray(
            $SQL,
            [
                'valueIsJSON' => true,
            ]
        );
    }
    /* fetchModelsByID() */



    /**
     * @param [CBID] $originalCBIDs
     *
     *      The original CBIDs array is always replaced with the array_values()
     *      of the array, meaning that if it is an associative array it will be
     *      converted without being sorted into a standard array. This only
     *      really matters if the $maintainPositions argument is true.
     *
     * @param bool $maintainPositions
     *
     *      If this argument is true then the array of models returned will have
     *      each model at its same index in the array as its CBID in the
     *      original CBIDs array. It's best to
     *
     * @return [model]
     */
    static function
    fetchModelsByID2(
        array $originalCBIDs,
        bool $maintainPositions = false
    ): array {
        if (
            empty($originalCBIDs)
        ) {
            return [];
        }

        if (
            $maintainPositions &&

            !cb_array_is_list(
                $originalCBIDs
            )
        ) {
            $CBIDs = array_values(
                $originalCBIDs
            );
        } else {
            $CBIDs = $originalCBIDs;
        }

        $CBIDsAsSQL = CBID::toSQL(
            $CBIDs
        );

        $SQL = <<<EOT

            SELECT
            CBModelVersions.modelAsJSON

            FROM
            CBModels

            JOIN
            CBModelVersions

            ON
            CBModels.ID = CBModelVersions.ID AND
            CBModels.version = CBModelVersions.version

            WHERE
            CBModels.ID IN ($CBIDsAsSQL)

        EOT;

        $valuesAsJSON = CBDB::SQLToArrayOfNullableStrings(
            $SQL
        );

        $models = array_map(
            function (
                $JSON
            ) {
                return CBConvert::JSONToValue(
                    $JSON
                );
            },
            $valuesAsJSON
        );

        $models = array_values(
            $models
        );

        if (
            $maintainPositions
        ) {
            $positionedModels = array_fill(
                0,
                count($originalCBIDs) - 1,
                null
            );

            foreach (
                $models as $currentModel
            ) {
                $modelCBID = CBModel::getCBID(
                    $currentModel
                );

                $index = array_search(
                    $modelCBID,
                    $CBIDs,
                    true
                );

                $positionedModels[$index] = $currentModel;
            }

            ksort(
                $positionedModels
            );

            $models = array_values(
                $positionedModels
            );
        }

        return $models;
    }
    /* fetchModelsByID2() */



    /**
     * Retreives a specific version of a model
     *
     * Scenarios:       Allow the users to view or revert to previous versions
     *                  of a web page. Allow external tables to reference
     *                  specific versions of a web page. For instance, the
     *                  published version may not be the most recently edited
     *                  version.
     * Usage Frequency: Occasionally
     */
    static function fetchModelByIDWithVersion($ID, $version) {
        $IDAsSQL = CBID::toSQL($ID);
        $versionAsSQL = (int)$version;

        $SQL = <<<EOT

            SELECT  `modelAsJSON`

            FROM    `CBModelVersions`

            WHERE   `ID` = {$IDAsSQL} AND
                    `version` = {$versionAsSQL}

        EOT;

        return CBDB::SQLToValue(
            $SQL,
            [
                'valueIsJSON' => true,
            ]
        );
    }
    /* fetchModelByIDWithVersion() */



    /**
     * @returng [object]
     *
     *      {
     *          CBModels_sitemapInformation_URL: string
     *          CBModels_sitemapInformation_modified: int (timestamp)
     *      }
     */
    static function
    fetchSitemapInformaton(
    ): array {
        $sitemapInformation = [
            (object)[
                'CBModels_sitemapInformation_URL' => '/',
                'CBModels_sitemapInformation_modified' => null,
            ],
        ];

        $SQL = <<<EOT

            SELECT

            CONCAT(
                "/",
                URI,
                "/"
            )
            AS CBModels_sitemapInformation_URL,

            modified
            AS CBModels_sitemapInformation_modified

            FROM
            ColbyPages

            WHERE
            published IS NOT NULL

        EOT;

        $results = CBDB::SQLToObjects(
            $SQL
        );

        $sitemapInformation = array_merge(
            $sitemapInformation,
            $results
        );

        $SQL = <<<EOT

            SELECT

            CBModels2_URLPath_column
            AS CBModels_sitemapInformation_URL,

            CBModels2_modified_column
            AS CBModels_sitemapInformation_modified

            FROM
            CBModels2_table

            WHERE
            CBModels2_URLPath_column != ''

        EOT;

        try {
            $results = CBDB::SQLToObjects(
                $SQL
            );
        } catch (
            Throwable $throwable
        ) {
            if (
                Colby::mysqli()->errno === 1146
            ) {
                error_log(
                    'CBModels2_table does not yet exist. Update website.' .
                    ' 4485aa38e03afe63dd3b934653bcc222a901e5d7'
                );

                $results = [];
            } else {
                throw $throwable;
            }
        }

        $sitemapInformation = array_merge(
            $sitemapInformation,
            $results
        );

        return $sitemapInformation;
    }
    /* fetchSitemapInformaton() */



    /**
     * Fetches the spec and model for use in tasks that analyze both.
     *
     * @param CBID $CBID
     *
     * @return object|false
     *
     *      {
     *          spec: object
     *          model: object
     *      }
     */
    static function fetchSpecAndModelByID(
        string $CBID
    ) {
        $CBIDAsSQL = CBID::toSQL($CBID);

        $SQL = <<<EOT

            SELECT  v.specAsJSON AS spec,
                    v.modelAsJSON AS model

            FROM    CBModels AS m

            JOIN    CBModelVersions AS v
              ON    m.ID = v.ID AND
                    m.version = v.version

            WHERE   m.ID = {$CBIDAsSQL}

        EOT;

        $value = CBDB::SQLToObjectNullable($SQL);

        if ($value === null) {
            return false;
        } else {
            $value->spec = json_decode($value->spec);
            $value->model = json_decode($value->model);

            return $value;
        }
    }
    /* fetchSpecAndModelByID() */



    /**
     * @param CBID $CBID
     *
     * @return object|null
     */
    static function
    fetchSpecByCBID(
        string $CBID
    ): ?stdClass {
        $specCBIDs = [$CBID];

        $specs = CBModels::fetchSpecsByID(
            $specCBIDs
        );

        if (empty($specs)) {
            return null;
        } else {
            return $specs[$CBID];
        }
    }
    /* fetchSpecByCBID() */



    /**
     * @deprecated notice version 675
     *
     *      Use CBModels::fetchSpecByCBID()
     *
     * @param ID $ID
     * @param [<arg> => <value>] $args
     *
     * @return model|false
     */
    static function
    fetchSpecByID(
        $ID,
        $args = []
    ) {
        $specs = CBModels::fetchSpecsByID([$ID], $args);

        if (empty($specs)) {
            return false;
        } else {
            return $specs[$ID];
        }
    }
    /* fetchSpecByID() */



    /**
     * @deprecated 2020_12_23 notice version 675
     *
     *      Use CBModels::fetchSpecByCBID()
     *
     * @param CBID $CBID
     *
     * @return object|null
     */
    static function
    fetchSpecByIDNullable(
        string $CBID
    ): ?stdClass {
        return CBModels::fetchSpecByCBID(
            $CBID
        );
    }
    /* fetchSpecByIDNullable() */



    /**
     * Retreives the current version of specs
     *
     * Scenarios:       Fetch the spec for a web page
     * Usage Frequency: Occasionally
     *
     * @return [model]
     *
     *      [
     *          ID => model,
     *          ID => model,
     *          ...
     *      ]
     */
    static function fetchSpecsByID(
        array $IDs,
        $args = []
    ): array {
        if (empty($IDs)) {
            return [];
        }

        $createSpecForIDCallback = null;

        extract($args, EXTR_IF_EXISTS);

        $IDsAsSQL = CBID::toSQL($IDs);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(m.ID)),
                    v.specAsJSON

            FROM    CBModels AS m

            JOIN    CBModelVersions AS v
                ON  m.ID = v.ID AND
                    m.version = v.version

            WHERE   m.ID IN ($IDsAsSQL)

        EOT;

        $specs = CBDB::SQLToArray(
            $SQL,
            [
                'valueIsJSON' => true
            ]
        );

        if (is_callable($createSpecForIDCallback)) {
            $existingIDs = array_keys($specs);
            $newIDs = array_diff($IDs, $existingIDs);
            $newSpecs = array_map($createSpecForIDCallback, $newIDs);

            foreach ($newSpecs as $newSpec) {
                $specs[$newSpec->ID] = $newSpec;
            }
        }

        return $specs;
    }
    /* fetchSpecsByID() */



    /**
     * Locks the rows for and fetches the version and created timestamp for a
     * set of IDs in preparation for an update. This will also insert rows for
     * IDs that don't exist.
     *
     * @param [ID] $IDs
     * @param int $modified
     *
     * @return [ID => object]
     *
     *      {
     *          created: int
     *          modified: int
     *          vesion: int
     *      }
     */
    private static function selectInitialDataForUpdateByID(
        array $IDs,
        int $modified
    ) {
        $IDsAsSQL = CBID::toSQL($IDs);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)) AS `ID`,
                    `created`,
                    `version`

            FROM    `CBModels`

            WHERE   `ID` IN ({$IDsAsSQL})

            FOR UPDATE

        EOT;

        $metaByID = CBDB::SQLToObjects($SQL, ['keyField' => 'ID']);

        foreach ($IDs as $ID) {
            if (isset($metaByID[$ID])) {
                $metaByID[$ID]->modified = $modified;
            } else {
                $metaByID[$ID] = (object)[
                    'created' => $modified,
                    'modified' => $modified,
                    'version' => 0,
                ];
            }
        }

        return $metaByID;
    }
    /* selectInitialDataForUpdateByID() */



    /**
     * @deprecated 2019_07_09
     *
     *      Just use:
     *
     *      (object)[
     *          'className' => <class name>,
     *          'ID' => <ID>,
     *      ]
     */
    static function modelWithClassName($className, $args = []) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $model = new stdClass();
        $model->className = (string)$className;

        if ($ID) {
            $model->ID = (string)$ID;
        }

        return $model;
    }
    /* modelWithClassName() */



    /**
     * @param string $ID
     * @param int $version
     *
     * @return null
     */
    static function revert($ID, $version) {
        $IDAsSQL = CBID::toSQL($ID);
        $versionAsSQL = intval($version);

        $SQL = <<<EOT

            SELECT  `specAsJSON`

            FROM    `CBModelVersions`

            WHERE   `ID` = $IDAsSQL AND
                    `version` = $versionAsSQL

        EOT;

        $spec = CBDB::SQLToValue($SQL, ['valueIsJSON' => true]);

        CBModels::save([$spec], /* force: */ true);
    }
    /* revert() */



    /**
     * Creates and saves models using specifications.
     *
     * Important: This function executes multiple queries each of which must
     * succeed for the save to be successful, so it should always be called
     * inside of a transaction.
     *
     *      CBDB::transaction(
     *          function () use ($spec) {
     *              CBModels::save($spec);
     *          }
     *      );
     *
     * @NOTE How model ID is determined:
     *
     *      The class CBModel_build() function can set the ID value on the model
     *      by either calculating it or copying it. Potentially no ID value
     *      needs to be set on the spec in this case, and if it is that ID value
     *      will be ignored.
     *
     *      If CBModel_build() returns a model with no ID value, the ID value on
     *      the spec must be set and will be used.
     *
     * @NOTE Reserved and required properties:
     *
     *      version: Both the spec and model will have their version properties
     *      set to the version assigned for this save.
     *
     *      It's a goal to move these properties off the spec and model objects
     *      to a meta object eventually.
     *
     * @param [object]|object $originalSpecs
     *
     *      All of the specs must have the same class name. For specs of
     *      different classes make multiple calls.
     *
     *      All of the specs will be clones, updated, and have their "version"
     *      property (default value is 0) incremented before they are saved. The
     *      specs passed in will not be changed.
     *
     * @param bool $force
     *
     *  Use this to disable version checking and force the model save. This
     *  should be used rarely and cautiously. Model import from CSV uses it.
     *  CBModels::revert() also uses it.
     *
     * @return null
     */
    static function
    save(
        $originalSpecs,
        $force = false
    ) {
        if (
            empty($originalSpecs)
        ) {
            return; // TODO: Why are we okay with this being empty? Document.
        }

        if (
            !is_array($originalSpecs)
        ) {
            $originalSpecs = [$originalSpecs];
        }

        /**
         * If the user has passed in an associative array, this function wants
         * a numeric array with the first item at index 0.
         */
        $originalSpecs = array_values(
            $originalSpecs
        );

        CBModels::save_checkSpecs(
            $originalSpecs
        );

        $sharedClassName = $originalSpecs[0]->className;
        $modified = time();

        $tuples = array_map(
            function (
                $originalSpec
            ) {

                /* we've already verified all specs have valid IDs */
                $ID = $originalSpec->ID;

                $upgradedSpec = CBModel::upgrade(
                    $originalSpec
                );

                $model = CBModel::build(
                    $upgradedSpec
                );

                return (object)[
                    'spec' => $upgradedSpec,
                    'model' => $model,
                ];
            },
            $originalSpecs
        );

        $IDs = array_map(
            function (
                $tuple
            ) {
                return $tuple->model->ID;
            },
            $tuples
        );

        /**
         * If any of the models being saved are in the cache, remove them now.
         */
        if (
            class_exists(
                'CBModelCache',
                false
                )
        ) {
            CBModelCache::uncacheByID(
                $IDs
            );
        }

        $initialDataByID = CBModels::selectInitialDataForUpdateByID(
            $IDs,
            $modified
        );

        array_walk(
            $tuples,
            function (
                $tuple
            ) use (
                $initialDataByID,
                $modified,
                $force
            ) {
                $ID = $tuple->model->ID;
                $mostRecentVersion = (int)$initialDataByID[$ID]->version;

                if (
                    $force !== true
                ) {
                    $specVersion = CBModel::valueAsInt(
                        $tuple->spec,
                        'version'
                    ) ?? 0;

                    if (
                        $specVersion !== $mostRecentVersion
                    ) {
                        throw new CBExceptionWithValue(
                            CBConvert::stringToCleanLine(<<<EOT

                                This model has been saved by another session
                                since you started editing it, saving your most
                                recent changes would overwrite the changes made
                                in that session. Reloading your editing page
                                will fetch the changes made by the other session
                                and allow you to save again. If someone else is
                                currently editing this model coordinate your
                                editing with them.

                            EOT),
                            $tuple->spec,
                            'a567dc90ccb59fb918ced4ae7f82e6d1b556f932'
                        );
                    }
                }

                /**
                 * 2016_06_29 In the future I would like to not set the version
                 * on the spec. The spec should theoretically remain unchanged.
                 * It's a data vs. metadata issue.
                 *
                 * 2016_07_03 No new properties should be set on the spec or the
                 * model. Use the meta property instead.
                 */
                $tuple->spec->version =
                $tuple->model->version =
                $mostRecentVersion + 1;

                $searchText = CBModel::toSearchText(
                    $tuple->model
                );

                $URLPath = CBModel::toURLPath(
                    $tuple->model
                );

                $tuple->meta = (object)[
                    'created' => $initialDataByID[$ID]->created,
                    'modified' => $modified,
                    'searchText' => $searchText,
                    'URLPath' => $URLPath,
                    'version' => $mostRecentVersion + 1,
                ];
            }
        );

        $functionName = "{$sharedClassName}::CBModels_willSave";

        if (
            is_callable($functionName)
        ) {
            $models = array_map(
                function ($tuple) {
                    return $tuple->model;
                },
                $tuples
            );

            call_user_func(
                $functionName,
                $models
            );
        } else {
            /* deprecated */
            $functionName = "{$sharedClassName}::modelsWillSave";

            if (is_callable($functionName)) {
                call_user_func(
                    $functionName,
                    $tuples
                );
            }
        }

        CBModels::saveToDatabase(
            $tuples
        );

        $priority = null;
        $delayInSeconds = 60; /* 1 minute */

        CBTasks2::restart(
            'CBModelPruneVersionsTask',
            $IDs,
            $priority,
            $delayInSeconds
        );
    }
    /* save() */



    /**
     * This function will throw an exception if there are multiple specs with
     * the same CBID.
     *
     * @param [object] $specs
     *
     * @return void
     */
    private static function save_checkSpecs(
        array $specs
    ): void {
        $duplicatedCBID = null;
        $previousCBIDs = [];
        $sharedClassName = null;

        foreach ($specs as $spec) {
            $className = CBModel::valueAsName(
                $spec,
                'className'
            );

            if ($className === null) {
                throw new CBExceptionWithValue(
                    'This spec has an invalid "className" property value.',
                    $spec,
                    'bc8c59476802e41b55f169764f9adb3cdacfb456'
                );
            }

            if ($sharedClassName === null) {
                $sharedClassName = $className;
            } else if ($className !== $sharedClassName) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        This spec does not have the same class name,
                        "{$sharedClassName}", as the previous specs in the
                        array of specs being saved.

                    EOT),
                    $spec,
                    '2a35f16d0b91fbc300ce7b4cbfab9858384e3466',
                );
            }

            $CBID = CBModel::valueAsCBID(
                $spec,
                'ID'
            );

            if ($CBID === null) {
                throw new CBExceptionWithValue(
                    'This spec does not have a valid "ID" property value.',
                    $spec,
                    '3754cbe6a23732edfaed0d357946840a1bf66bb6'
                );
            }

            $CBIDIsDuplicated = in_array(
                $CBID,
                $previousCBIDs
            );

            if ($CBIDIsDuplicated) {
                $duplicatedCBID = $CBID;
                break;
            }

            array_push(
                $previousCBIDs,
                $CBID
            );
        }
        /* foreach */

        if ($duplicatedCBID !== null) {
            $specsWithDuplicatedCBID = array_values(
                array_filter(
                    $specs,
                    function ($spec) use ($duplicatedCBID) {
                        $currentCBID = CBModel::valueAsCBID(
                            $spec,
                            'ID'
                        );

                        return $currentCBID === $duplicatedCBID;
                    }
                )
            );

            throw new CBExceptionWithValue(
                'Multiple saved specs have the same "ID" property value.',
                $specsWithDuplicatedCBID,
                '3640ae9ab3459c0995e4bd210cfc38c0fd4e79d7'
            );
        }
    }
    /* save_checkSpecs() */



    /**
     * Saves model data
     *
     * This function saves the spec and the model as a new version. You must
     * call one of the `ForUpdate` functions before calling this function and
     * you must save _all_ of the models you specified in that call.
     *
     * This function is meant to be called during a database transaction.
     *
     * @param [object] $tuples
     *
     *      {
     *          spec: object
     *          model: object
     *          meta: object
     *      }
     */
    private static function
    saveToDatabase(
        array $tuples
    ) {
        /* CBModelVersions */



        $values = array_map(
            function ($tuple) {
                $IDAsSQL = CBID::toSQL(
                    $tuple->model->ID
                );

                $modelAsJSONAsSQL = CBDB::stringToSQL(
                    json_encode(
                        $tuple->model
                    )
                );

                $specAsJSONAsSQL = CBDB::stringToSQL(
                    json_encode(
                        $tuple->spec
                    )
                );

                return (
                    "(" .
                    "{$IDAsSQL}," .
                    "{$tuple->meta->version}," .
                    "{$modelAsJSONAsSQL}," .
                    "{$specAsJSONAsSQL}," .
                    "{$tuple->meta->modified}" .
                    ")"
                );
            },
            $tuples
        );

        $values = implode(
            ',',
            $values
        );

        $SQL = <<<EOT

            INSERT INTO
            CBModelVersions

            (
                ID,
                version,
                modelAsJSON,
                specAsJSON,
                timestamp
            )

            VALUES
            {$values}

        EOT;

        Colby::query(
            $SQL
        );



        /* CBModels */



        $values = array_map(
            function (
                $tuple
            ) {
                $IDAsSQL = CBID::toSQL(
                    $tuple->model->ID
                );

                $classNameAsSQL = CBDB::stringToSQL(
                    $tuple->model->className
                );

                $titleAsSQL = CBDB::stringToSQL(
                    CBModel::valueToString(
                        $tuple->model,
                        'title'
                    )
                );

                return (
                    "(" .
                    "{$IDAsSQL}," .
                    "{$classNameAsSQL}," .
                    "{$tuple->meta->created}," .
                    "{$tuple->meta->modified}," .
                    "{$titleAsSQL}," .
                    "{$tuple->meta->version}" .
                    ")"
                );
            },
            $tuples
        );

        $values = implode(
            ',',
            $values
        );

        /**
         * Created temporary CBModels table.
         */

        CBModelsTable::create(
            /* temporary: */ true
        );

        /**
         * Insert data for all models into the temporary CBModels table.
         */

        Colby::query(
            "INSERT INTO CBModelsTemp VALUES {$values}"
        );

        /**
         * For models that already exist in the CBModels table, transfer their
         * data from the temporary CBModels table into the CBModels table.
         */

        $SQL = <<<EOT

            UPDATE  CBModels      AS m

            JOIN    CBModelsTemp  AS t
                    ON m.ID = t.ID

            SET     m.className = t.className,
                    m.modified = t.modified,
                    m.title = t.title,
                    m.version = t.version

        EOT;

        Colby::query(
            $SQL
        );

        /**
         * For models that don't yet exist in the CBModels table, insert their
         * data into the CBModels table.
         */

        $SQL = <<<EOT

            INSERT INTO CBModels

            SELECT      ID,
                        className,
                        created,
                        modified,
                        title,
                        version

            FROM        CBModelsTemp

            WHERE       version = 1

        EOT;

        Colby::query(
            $SQL
        );



        /**
         * Delete the temporary CBModels table.
         */

        Colby::query(
            'DROP TEMPORARY TABLE CBModelsTemp'
        );



        /* CBModels2 */



        $values = array_map(
            function (
                $tuple
            ) {
                $IDAsSQL = CBID::toSQL(
                    $tuple->model->ID
                );

                $classNameAsSQL = CBDB::stringToSQL(
                    $tuple->model->className
                );

                $searchTextAsSQL = CBDB::stringToSQL(
                    $tuple->meta->searchText
                );

                $URLPathAsSQL = CBDB::stringToSQL(
                    $tuple->meta->URLPath
                );

                return (
                    "(" .
                    "{$IDAsSQL}," .
                    "{$classNameAsSQL}," .
                    "{$tuple->meta->created}," .
                    "{$tuple->meta->modified}," .
                    "{$tuple->meta->version}," .
                    "{$searchTextAsSQL}," .
                    "{$URLPathAsSQL}" .
                    ")"
                );
            },
            $tuples
        );

        $values = implode(
            ',',
            $values
        );

        $SQL = <<<EOT

            INSERT INTO
            CBModels2_table

            VALUES
            {$values}

            ON DUPLICATE KEY UPDATE

            CBModels2_className_column = CBModels2_className_column,
            CBModels2_created_column = CBModels2_created_column,
            CBModels2_modified_column = CBModels2_modified_column,
            CBModels2_version_column = CBModels2_version_column,
            CBModels2_searchText_column = CBModels2_searchText_column,
            CBModels2_URLPath_column = CBModels2_URLPath_column

        EOT;

        /**
         * @TODO 2021_12_04
         *
         *      Remove try/catch block in Colby version 676
         */

        try {
            Colby::query(
                $SQL
            );
        } catch (
            Throwable $throwable
        ) {
            if (
                Colby::mysqli()->errno === 1146
            ) {
                error_log(
                    'CBModels2_table does not yet exist. Update website.'
                );

                return null;
            } else {
                throw $throwable;
            }
        }
    }
    /* saveToDatabase() */

}
