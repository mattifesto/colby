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
final class CBModels {

    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * This function is a heavy-duty delete. It will remove the model, and it
     * will also remove the data store. If you need the files in the data store
     * you should not be deleting the model.
     *
     * @param object $args
     *
     *      {
     *          ID: hex160
     *      }
     *
     * @return null
     */
    static function CBAjax_deleteByID(stdClass $args) {
        $ID = CBModel::value($args, 'ID');

        CBDB::transaction(function () use ($ID) {
            CBModels::deleteByID($ID);
        });
    }
    /* CBAjax_deleteByID() */


    /**
     * @return string
     */
    static function CBAjax_deleteByID_group() {
        return 'Administrators';
    }
    /* CBAjax_deleteByID_group() */


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
    static function CBAjax_save_group(): string {
        return 'Public';
    }
    /* CBAjax_save_group() */


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
            return ColbyUser::currentUserIsMemberOfGroup('Administrators');
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
     * @param hex160|[hex160] $IDs
     *  All of the referenced models must have the same class name. Make
     *  separate calls for each class name.
     *
     * @return null
     */
    static function deleteByID($IDs) {
        if (empty($IDs)) { return; }

        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        $IDsForSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT DISTINCT `className`
            FROM            `CBModels`
            WHERE           `ID` IN ({$IDsForSQL})

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

            if (
                is_callable($function = "{$classNames[0]}::CBModels_willDelete")
            ) {
                call_user_func($function, $IDs);
            } else  if (
                is_callable($function = "{$classNames[0]}::modelsWillDelete")
            ) {
                call_user_func($function, $IDs);
            }
        }

        $SQL = <<<EOT

            DELETE  `CBModels`, `CBModelVersions`
            FROM    `CBModels`
            JOIN    `CBModelVersions`
            ON      `CBModelVersions`.`ID` = `CBModels`.`ID`
            WHERE   `CBModels`.`ID` IN ($IDsForSQL)

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
     * @param [hex160] $IDs
     *
     * @return [int]
     */
    private static function fetchCreatedTimestampsForIDs(array $IDs) {
        if (empty($IDs)) { return []; }

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)), `timestamp`
            FROM    `CBModelVersions`
            WHERE   `ID` in ({$IDsAsSQL}) AND `version` = 0

EOT;

        return CBDB::SQLtoArray($SQL);
    }
    /* fetchCreatedTimestampsForIDs() */


    /**
     * @deprecated use fetchModelByIDNullable()
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
     * @NOTE 2018.06.20
     *
     *      This function is a transition function to move callers away from
     *      fetchModelByID() which returns false if there is no model found.
     *
     *      The fetchModelByID() function was created before PHP 7 and the
     *      introduction of nullable return types.
     *
     *      Eventually, this function will be deprecated and fetchModelByID()
     *      will be reintroduced returning a nullable object.
     *
     * @param ID $ID
     *
     * @return ?model
     */
    static function fetchModelByIDNullable(string $ID): ?stdClass {
        $models = CBModels::fetchModelsByID([$ID]);

        if (empty($models)) {
            return null;
        } else {
            return $models[$ID];
        }
    }
    /* fetchModelByIDNullable() */


    /**
     * This function will return all the models with a given class name. This
     * function should be used mindfully because there are some class names with
     * a high number of models. This function is intended to be used in cases
     * where the caller is aware that the total number of models that will be
     * returned is likely to be reasonable.
     *
     * @param string $className
     *
     * @return [ID => model]
     */
    static function fetchModelsByClassName(string $className): array {
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

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
    }
    /* fetchModelsByClassName() */


    /**
     * @param string $className
     *
     * @return [model]
     */
    static function fetchModelsByClassName2(string $className): array {
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

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT  LOWER(HEX(m.ID)),
                    v.modelAsJSON
            FROM    CBModels AS m
            JOIN    CBModelVersions AS v ON
                    m.ID = v.ID AND
                    m.version = v.version
            WHERE   m.ID IN ($IDsAsSQL)

EOT;

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
    }
    /* fetchModelsByID() */


    /**
     * @param [ID] $IDs
     *
     * @return [model]
     */
    static function fetchModelsByID2(array $IDs): array {
        if (empty($IDs)) {
            return [];
        }

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT  v.modelAsJSON
            FROM    CBModels as m
            JOIN    CBModelVersions as v ON
                    m.ID = v.ID AND
                    m.version = v.version
            WHERE   m.ID IN ($IDsAsSQL)

EOT;

        $valuesAsJSON = CBDB::SQLToArrayOfNullableStrings($SQL);

        return array_map(
            function ($JSON) {
                return CBConvert::JSONToValue($JSON);
            },
            $valuesAsJSON
        );
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
        $IDAsSQL = CBHex160::toSQL($ID);
        $versionAsSQL = (int)$version;
        $SQL = <<<EOT

            SELECT  `modelAsJSON`
            FROM    `CBModelVersions`
            WHERE   `ID` = {$IDAsSQL} AND
                    `version` = {$versionAsSQL}

EOT;

        return CBDB::SQLToValue($SQL, ['valueIsJSON' => true]);
    }
    /* fetchModelByIDWithVersion() */


    /**
     * Fetches the spec and model for use in tasks that analyze both.
     *
     * @param hex160 $ID
     *
     * @return object|false
     *
     *      {
     *          spec: object
     *          model: object
     *      }
     */
    static function fetchSpecAndModelByID($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT  `v`.`specAsJSON` AS `spec`, `v`.`modelAsJSON` AS `model`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v`
              ON    `m`.`ID` = `v`.`ID` AND
                    `m`.`version` = `v`.`version`
            WHERE   `m`.`ID` = {$IDAsSQL}

EOT;

        $value = CBDB::SQLToObject($SQL);

        if ($value) {
            $value->spec = json_decode($value->spec);
            $value->model = json_decode($value->model);
        }

        return $value;
    }
    /* fetchSpecAndModelByID() */


    /**
     * @deprecated use fetchSpecByIDNullable()
     *
     * @param ID $ID
     * @param [<arg> => <value>] $args
     *
     * @return model|false
     */
    static function fetchSpecByID($ID, $args = []) {
        $specs = CBModels::fetchSpecsByID([$ID], $args);

        if (empty($specs)) {
            return false;
        } else {
            return $specs[$ID];
        }
    }
    /* fetchSpecByID() */


    /**
     * @NOTE 2018_07_15
     *
     *      This function is a transition function to move callers away from
     *      fetchSpecByID() which returns false if there is no model found.
     *
     *      The fetchSpecByID() function was created before PHP 7 and the
     *      introduction of nullable return types.
     *
     *      Eventually, this function will be deprecated and fetchSpecByID()
     *      will be reintroduced returning a nullable object.
     *
     * @param ID $ID
     *
     * @return ?model
     */
    static function fetchSpecByIDNullable(string $ID): ?stdClass {
        $specs = CBModels::fetchSpecsByID([$ID]);

        if (empty($specs)) {
            return null;
        } else {
            return $specs[$ID];
        }
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
     *          hex160 => model,
     *          hex160 => model,
     *          ...
     *      ]
     */
    static function fetchSpecsByID(array $IDs, $args = []): array {
        if (empty($IDs)) {
            return [];
        }

        $createSpecForIDCallback = null;
        extract($args, EXTR_IF_EXISTS);

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`m`.`ID`)), `v`.`specAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v`
            ON      `m`.`ID` = `v`.`ID` AND
                    `m`.`version` = `v`.`version`
            WHERE   `m`.`ID` IN ($IDsAsSQL)

EOT;
        $specs = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

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
        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)) AS `ID`, `created`, `version`
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
     * @param hex160 $ID
     * @param int $version
     *
     * @return null
     */
    static function revert($ID, $version) {
        $IDAsSQL = CBHex160::toSQL($ID);
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
     * @return void
     */
    static function CBAjax_revert(stdClass $args): void {
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
    static function CBAjax_revert_group(): string {
        return 'Administrators';
    }
    /* CBAjax_revert_group() */


    /**
     * Creates and saves models using specifications.
     *
     * Important: This function executes multiple queries each of which must
     * succeed for the save to be successful, so it should always be called
     * inside of a transaction.
     *
     *      Colby::query('START TRANSACTION');
     *      CBModels::save([$spec]);
     *      Colby::query('COMMIT');
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
     * @param [object]|object $specs
     *
     *  All of the specs must have the same class name. For specs of different
     *  classes make multiple calls.
     *
     * @param bool $force
     *
     *  Use this to disable version checking and force the model save. This
     *  should be used rarely and cautiously. Model import from CSV uses it.
     *  CBModels::revert() also uses it.
     *
     * @return null
     */
    static function save($specs, $force = false) {
        if (empty($specs)) {
            return; // TODO: Why are we okay with this being empty? Document.
        }

        if (!is_array($specs)) {
            $specs = [$specs];
        }

        $firstSpec = reset($specs);

        if (empty($firstSpec->className)) {
            throw new Exception(
                'The first spec does not have its `className` propery set.'
            );
        } else {
            $sharedClassName = $firstSpec->className;
        }

        $modified = time();

        $tuples = array_map(
            function ($spec) use ($sharedClassName) {
                $ID = CBModel::valueAsID($spec, 'ID');

                if ($ID === null) {
                    throw new Exception(
                        "A {$spec->className} spec being saved does not " .
                        "have an ID."
                    );
                }

                $model = CBModel::build($spec);

                if ($model === null) {
                    throw new Exception(
                        "A {$spec->className} spec being saved generated " .
                        "a null model."
                    );
                }

                if ($model->className !== $sharedClassName) {
                    throw new Exception(
                        'All specs being saved must have the same className.'
                    );
                }

                return (object)[
                    'spec' => $spec,
                    'model' => $model,
                ];
            },
            $specs
        );

        $IDs = array_map(
            function ($tuple) {
                return $tuple->model->ID;
            },
            $tuples
        );

        /**
         * If any of the models being saved are in the cache, remove them now.
         */
        if (class_exists('CBModelCache', false)) {
            CBModelCache::uncacheByID($IDs);
        }

        $initialDataByID = CBModels::selectInitialDataForUpdateByID(
            $IDs,
            $modified
        );

        array_walk(
            $tuples,
            function ($tuple) use ($initialDataByID, $modified, $force) {
                $ID = $tuple->model->ID;
                $mostRecentVersion = (int)$initialDataByID[$ID]->version;

                if ($force !== true) {
                    $specVersion = CBModel::valueAsInt(
                        $tuple->spec,
                        'version'
                    ) ?? 0;

                    if ($specVersion !== $mostRecentVersion) {
                        throw new CBModelVersionMismatchException();
                    }
                }

                /**
                 * 2016_06_29 In the future I would like to not set the version on
                 * the spec. The spec should theoretically remain unchanged. It's a
                 * data vs. metadata issue.
                 *
                 * 2016_07_03 No new properties should be set on the spec or the
                 * model. Use the meta property instead.
                 */
                $tuple->spec->version =
                $tuple->model->version =
                $mostRecentVersion + 1;

                $tuple->meta = (object)[
                    'created' => $initialDataByID[$ID]->created,
                    'modified' => $modified,
                    'version' => $mostRecentVersion + 1,
                ];
            }
        );

        if (
            is_callable($function = "{$sharedClassName}::CBModels_willSave")
        ) {
            $models = array_map(function ($tuple) {
                return $tuple->model;
            }, $tuples);

            call_user_func($function, $models);
        } else if (
            is_callable($function = "{$sharedClassName}::modelsWillSave")
        ) { /* deprecated */
            call_user_func($function, $tuples);
        }

        CBModels::saveToDatabase($tuples);

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
    private static function saveToDatabase(array $tuples) {
        /* 1: CBModelVersions */

        $values = array_map(
            function ($tuple) {
                $IDAsSQL = CBHex160::toSQL($tuple->model->ID);

                $modelAsJSONAsSQL = CBDB::stringToSQL(
                    json_encode($tuple->model)
                );

                $specAsJSONAsSQL = CBDB::stringToSQL(
                    json_encode($tuple->spec)
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

        $values = implode(',', $values);

        $SQL = <<<EOT

            INSERT INTO CBModelVersions
            (
                ID,
                version,
                modelAsJSON,
                specAsJSON,
                timestamp
            )
            VALUES {$values}

EOT;

        Colby::query($SQL);

        /* 2: CBModels */

        $values = array_map(
            function ($tuple) {
                $IDAsSQL = CBHex160::toSQL($tuple->model->ID);
                $classNameAsSQL = CBDB::stringToSQL($tuple->model->className);
                $titleAsSQL = CBDB::stringToSQL(
                    CBModel::valueToString($tuple->model, 'title')
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

        $values = implode(',', $values);

        /**
         * Created temporary CBModels table.
         */

        CBModelsTable::create(/* temporary: */ true);

        /**
         * Insert data for all models into the temporary CBModels table.
         */

        Colby::query("INSERT INTO CBModelsTemp VALUES {$values}");

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

        Colby::query($SQL);

        /**
         * For models that don't yet exist in the CBModels table, insert their
         * data into the CBModels table.
         */

        $SQL = <<<EOT

            INSERT INTO CBModels
            SELECT      ID, className, created, modified, title, version
            FROM        CBModelsTemp
            WHERE       version = 1

EOT;

        Colby::query($SQL);

        /**
         * Delete the temporary CBModels table.
         */

        Colby::query('DROP TEMPORARY TABLE CBModelsTemp');
    }
    /* saveToDatabase() */
}
