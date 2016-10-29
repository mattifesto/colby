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

    /**
     * Creates either the permanent or a temporary CBModels table.
     *
     * @return null
     */
    public static function createModelsTable($temporary = false) {
        $name = $temporary ? 'CBModelsTemp' : 'CBModels';
        $options = $temporary ? 'TEMPORARY' : '';
        $SQL = <<<EOT

            CREATE {$options} TABLE IF NOT EXISTS `{$name}` (
                `ID`        BINARY(20) NOT NULL,
                `className` VARCHAR(80) NOT NULL,
                `created`   BIGINT NOT NULL,
                `modified`  BIGINT NOT NULL,
                `title`     TEXT NOT NULL,
                `version`   BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY                 (`ID`),
                KEY `className_created`     (`className`, `created`),
                KEY `className_modified`    (`className`, `modified`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * Delete models.
     *
     * Important: This function executes multiple queries each of which must
     * succeed for the save to be successful, so it should always be called
     * inside of a transaction.
     *
     *      Colby::query('START TRANSACTION');
     *      CBModels::deleteModelsByID($ID);
     *      Colby::query('COMMIT');
     *
     * @param [hex160] $IDs
     *  All of the referenced models must have the same class name. Make
     *  separate calls for each class name.
     *
     * @return null
     */
    public static function deleteModelsByID(array $IDs) {
        if (empty($IDs)) { return; }

        $IDsForSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT DISTINCT `className`
            FROM            `CBModels`
            WHERE           `ID` IN ({$IDsForSQL})

EOT;

        $classNames = CBDB::SQLtoArray($SQL);

        if (empty($classNames)) { return; }

        if (count($classNames) > 1) {
            $classNames = implode(', ', $classNames);
            $method = __METHOD__;
            throw new RuntimeException("The IDs provided to {$method} have multiple class names: {$classNames}.");
        }

        if (is_callable($function = "{$classNames[0]}::modelsWillDelete")) {
            call_user_func($function, $IDs);
        }

        $SQL = <<<EOT

            DELETE  `CBModels`, `CBModelVersions`
            FROM    `CBModels`
            JOIN    `CBModelVersions` ON `CBModelVersions`.`ID` = `CBModels`.`ID`
            WHERE   `CBModels`.`ID` IN ($IDsForSQL)

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    static function deleteModelsByIDForAjax() {
        $response = new CBAjaxResponse();

        $IDs = json_decode($_POST['IDsAsJSON']);

        CBModels::deleteModelsByID($IDs);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function deleteModelsByIDForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

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

    /**
     * Retreives the current version of a model
     *
     * Scenarios:       Fetch the model for a web page
     * Usage Frequency: Often
     *
     * @return stdClass|false
     */
    public static function fetchModelByID($ID) {
        $models = self::fetchModelsByID([$ID]);

        if (empty($models)) {
            return false;
        } else {
            return $models[$ID];
        }
    }

    /**
     * Retreives the current version of models
     *
     * Scenarios:       Fetch the model for a web page
     * Usage Frequency: Often
     *
     * @return [<hex160> => {stdClass}, ...]
     */
    public static function fetchModelsByID(array $IDs) {
        if (empty($IDs)) { return []; }

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`m`.`ID`)), `v`.`modelAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE   `m`.`ID` IN ($IDsAsSQL)

EOT;

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
    }

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
    public static function fetchModelByIDWithVersion($ID, $version) {
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

    /**
     * Retreives the current version of a spec
     *
     * Scenarios:       Fetch the spec for a web page
     * Usage Frequency: Occasionally
     *
     * @return {stdClass} | false
     */
    public static function fetchSpecByID($ID, $args = []) {
        $specs = CBModels::fetchSpecsByID([$ID], $args);

        if (empty($specs)) {
            return false;
        } else {
            return $specs[$ID];
        }
    }

    /**
     * Retreives the current version of specs
     *
     * Scenarios:       Fetch the spec for a web page
     * Usage Frequency: Occasionally
     *
     * @return [<hex160> => {stdClass}, ...]
     */
    public static function fetchSpecsByID(array $IDs, $args = []) {
        if (empty($IDs)) { return []; }

        $createSpecForIDCallback = null;
        extract($args, EXTR_IF_EXISTS);

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`m`.`ID`)), `v`.`specAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE   `m`.`ID` IN ($IDsAsSQL)

EOT;
        $specs = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

        if (is_callable($createSpecForIDCallback)) {
            $existingIDs = array_keys($specs);
            $newIDs = array_diff($IDs, $existingIDs);
            $newSpecs = array_map($createSpecForIDCallback, $newIDs);
            foreach($newSpecs as $newSpec) { $specs[$newSpec->ID] = $newSpec; }
        }

        return $specs;
    }

    /**
     * @return {stdClass}
     */
    public static function fetchSpecForAjax() {
        $response = new CBAjaxResponse();
        $spec = CBModels::fetchSpecByID($_POST['ID']);
        $className = ($spec !== false) ? $spec->className : $_POST['className'];
        $info = CBModelClassInfo::classNameToInfo($className);

        if (!ColbyUser::current()->isOneOfThe($info->userGroup)) {
            $response->message = "You do not have permission to edit this model.";
            $response->wasSuccessful = false;
        } else {
            if ($spec != false) {
                $response->spec = $spec;
            }
            $response->wasSuccessful = true;
        }

        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchSpecForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function install() {
        include __DIR__ . '/CBModelsInstall.php';
    }

    /**
     * Locks the rows for and fetches the version and created timestamp for a
     * set of IDs in preparation for an update. This will also insert rows for
     * IDs that don't exist.
     */
    private static function selectInitialDataForUpdateByID(array $IDs, $modified) {
        $IDsAsSQL = CBHex160::toSQL($IDs);
        $modifiedAsSQL = (int)$modified;
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

    /**
     * Creates a new model with a class name and optionally an ID.
     */
    public static function modelWithClassName($className, $args = []) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $model = new stdClass();
        $model->className = (string)$className;

        if ($ID) {
            $model->ID = (string)$ID;
        }

        return $model;
    }

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
     * @param [{stdClass}] $specs
     *  All of the specs must have the same class name. For specs of different
     *  classes make multiple calls.
     * @param bool $force
     *  Use this to disable version checking and force the model save. This
     *  should be used rarely and cautiously. Model import from CSV uses it.
     *
     * @return null
     */
    public static function save(array $specs, $force = false) {
        if (empty($specs)) { return; }

        $className = reset($specs)->className;
        $modified = time();

        $tuples = array_map(function ($spec) use ($className) {
            if ($spec->className !== $className) {
                throw new InvalidArgumentException('specs: All specs must have the same className.');
            }

            $model = call_user_func("{$className}::specToModel", $spec);

            if (empty($model->ID)) {
                $model->ID = $spec->ID;
            }

            if (!isset($model->title)) {
                $model->title = CBModel::value($spec, 'title', null, 'trim');
            }

            return (object)[
                'spec' => $spec,
                'model' => $model,
            ];
        }, $specs);

        $IDs = array_map(function ($tuple) { return $tuple->model->ID; }, $tuples);
        $initialDataByID = CBModels::selectInitialDataForUpdateByID($IDs, $modified);

        array_walk($tuples, function ($tuple) use ($initialDataByID, $modified, $force) {
            $ID = $tuple->model->ID;
            $mostRecentVersion = (int)$initialDataByID[$ID]->version;

            if ($force !== true) {
                $specVersion = CBModel::value($tuple->spec, 'version', 0);

                if ($specVersion !== $mostRecentVersion) {
                    throw new CBModelVersionMismatchException();
                }
            }

            $tuple->model->created = $initialDataByID[$ID]->created;
            $tuple->model->modified = $modified;

            /**
             * 2016.06.29 In the future I would like to not set the version on
             * the spec. The spec should theoretically remain unchanged. It's a
             * data vs. metadata issue.
             *
             * 2016.07.03 No new properties should be set on the spec or the
             * model. Use the meta property instead.
             */
            $tuple->spec->version = $tuple->model->version = $mostRecentVersion + 1;

            $tuple->meta = (object)[
                'created' => $initialDataByID[$ID]->created,
                'modified' => $modified,
                'version' => $mostRecentVersion + 1,
            ];
        });

        if (is_callable($function = "{$className}::modelsWillSave")) {
            call_user_func($function, $tuples);
        }

        CBModels::saveToDatabase($tuples);
    }

    /**
     * @return {stdClass}
     */
    public static function saveForAjax() {
        $response = new CBAjaxResponse();
        $spec = json_decode($_POST['specAsJSON']);

        Colby::query('START TRANSACTION');

        try {
            CBModels::save([$spec]);
            Colby::query('COMMIT');
            $response->wasSuccessful = true;
        } catch (Exception $exception) {
            Colby::query('ROLLBACK');
            throw $exception;
        }

        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function saveForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * Saves model data
     *
     * This function saves the spec and the model as a new version. You must
     * call one of the `ForUpdate` functions before calling this function and
     * you must save _all_ of the models you specified in that call.
     *
     * This function is meant to be called during a database transaction.
     *
     * @param [{'spec':stdClass, 'model':stdClass, 'meta':stdClass}] $tuples
     */
    private static function saveToDatabase(array $tuples) {
        /* 1: CBModelVersions */

        $values = array_map(function($tuple) {
            $IDAsSQL = CBHex160::toSQL($tuple->model->ID);
            $modelAsJSONAsSQL = CBDB::stringToSQL(json_encode($tuple->model));
            $specAsJSONAsSQL = CBDB::stringToSQL(json_encode($tuple->spec));

            return "({$IDAsSQL}, {$tuple->meta->version}, {$modelAsJSONAsSQL}, {$specAsJSONAsSQL}, {$tuple->meta->modified})";
        }, $tuples);
        $values = implode(',', $values);

        Colby::query("INSERT INTO `CBModelVersions` VALUES {$values}");

        /* 2: CBModels */

        $values = array_map(function($tuple) {
            $IDAsSQL = CBHex160::toSQL($tuple->model->ID);
            $classNameAsSQL = CBDB::stringToSQL($tuple->model->className);
            $titleAsSQL = CBDB::stringToSQL(CBModel::value($tuple->model, 'title', ''));

            return "({$IDAsSQL}, {$classNameAsSQL}, {$tuple->meta->created}, {$tuple->meta->modified}, {$titleAsSQL}, {$tuple->meta->version})";
        }, $tuples);
        $values = implode(',', $values);

        CBModels::createModelsTable(/* temporary: */ true);
        Colby::query("INSERT INTO `CBModelsTemp` VALUES {$values}");

        $SQL = <<<EOT

            UPDATE  `CBModels`      AS `m`
            JOIN    `CBModelsTemp`  AS `t` ON `m`.`ID` = `t`.`ID`
            SET     `m`.`className` = `t`.`className`,
                    `m`.`modified` = `t`.`modified`,
                    `m`.`title` = `t`.`title`,
                    `m`.`version` = `t`.`version`

EOT;

        Colby::query($SQL);

        $SQL = <<<EOT

            INSERT INTO `CBModels`
            SELECT      `ID`, `className`, `created`, `modified`, `title`, `version`
            FROM        `CBModelsTemp`
            WHERE       `version` = 1

EOT;

        Colby::query($SQL);

        Colby::query('DROP TEMPORARY TABLE `CBModelsTemp`');
    }

    /**
     * NOTE: This function executes multiple queries each of which must
     * succeed for the save to be successful, so it should always be called
     * inside of a transaction.
     *
     * @param [{spec:stdClass, model:stdClass, version:int|'force'}]
     *
     * @return null
     */
    public static function saveTuples(array $tuples) {
        if (empty($tuples)) { return; }

        $className = reset($tuples)->model->className;
        $IDs = array_map(function ($tuple) { return $tuple->model->ID; }, $tuples);
        $metaByID = CBModels::selectInitialDataForUpdateByID($IDs, time());

        $dbtuples = array_map(function ($tuple) use ($className, $metaByID) {
            $ID = $tuple->model->ID;

            if ($tuple->model->className !== $className) {
                throw new InvalidArgumentException('All models must have the same className.');
            }

            if ($tuple->version !== 'force') {
                if ($tuple->version !== $metaByID[$ID]->version) {
                    throw new CBModelVersionMismatchException();
                }
            }

            return (object)[
                'spec' => $tuple->spec,
                'model' => $tuple->model,
                'meta' => (object)[
                    'created' => $metaByID[$ID]->created,
                    'modified' => $metaByID[$ID]->modified,
                    'version' => $metaByID[$ID]->version + 1,
                ],
            ];
        }, $tuples);

        if (is_callable($function = "{$className}::modelsWillSave")) {
            call_user_func($function, $tuples);
        }

        CBModels::saveToDatabase($dbtuples);
    }

    /**
     * @deprecated use CBModel::value()
     *
     * @return string
     */
    public static function specToTitle(stdClass $spec) {
        return CBModel::value($spec, 'title', null, 'trim');
    }
}
