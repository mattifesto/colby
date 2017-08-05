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
     * @param stdClass $model
     *
     * @return bool
     *      Returns true if the current user can read the model via Ajax;
     *      otherwise false. If permissions aren't implement by the model class
     *      the default is that Administrators can read and others can't.
     */
    static function currentUserCanRead(stdClass $model) {
        if (empty($model->className)) {
            return false;
        }

        if (is_callable($function = "{$model->className}::currentUserCanRead")) {
            return call_user_func($function, $model);
        } else {
            return ColbyUser::isMemberOfGroup(ColbyUser::currentUserId(), 'Administrators');
        }
    }

    /**
     * @param stdClass $model
     *
     * @return bool
     *      Returns true if the current user can write the model via Ajax;
     *      otherwise false. If permissions aren't implement by the model class
     *      the default is that Administrators can write and others can't.
     */
    static function currentUserCanWrite(stdClass $model) {
        if (empty($model->className)) {
            return false;
        }

        if (is_callable($function = "{$model->className}::currentUserCanWrite")) {
            return call_user_func($function, $model);
        } else {
            return ColbyUser::isMemberOfGroup(ColbyUser::currentUserId(), 'Administrators');
        }
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
     * @deprecated use CBModels::deleteByID()
     */
    static function deleteModelsByID($IDs) {
        return CBModels::deleteByID($IDs);
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
     * @param hex160 $ID
     *
     * @return stdClass
     */
    static function fetchModelVersionsByID($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT      `version`, `timestamp`, `specAsJSON`, `modelAsJSON`
            FROM        `CBModelVersions`
            WHERE       `ID` = {$IDAsSQL}
            ORDER BY    `version` DESC

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @param hex160 $_POST['ID']
     *
     * @return null
     */
    static function fetchModelVersionsByIDForAjax() {
        $response = new CBAjaxResponse();
        $ID = $_POST['ID'];
        $response->versions = CBModels::fetchModelVersionsByID($ID);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function fetchModelVersionsByIDForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * Fetches the spec and model for use in tasks that analyze both.
     *
     * @param hex160 $ID
     *
     * @return object
     *
     *      object ->spec
     *      object ->model
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
     * Fetches a spec by ID.
     *
     * @param hex160 $_POST['ID']
     *
     * @return null
     *
     *      Ajax response properties:
     *
     *      object? spec
     *          The spec property will be set to the spec if the spec exists and
     *          the current user has read permissions; otherwise it will not be
     *          set.
     *
     *      Ajax errors:
     *
     *      If the spec exists but user does not have permission to read the it,
     *      this is treated as an error. When you use this API it is assumed you
     *      have permission to read the spec, if you don't it's an error.
     *
     *      If the spec doesn't exist but the user wouldn't have permission to
     *      read it, there is no error from this API. If an app needs to predict
     *      permissions it should use another API.
     */
    static function fetchSpecForAjax() {
        $response = new CBAjaxResponse();
        $ID = $_POST['ID'];
        $spec = CBModels::fetchSpecByID($ID);

        if (empty($spec)) {
            $response->message = "A spec was not found with ID: {$ID}.";
            $response->wasSuccessful = true;
            goto done;
        }

        $info = CBModelClassInfo::classNameToInfo($spec->className);

        // NOTE: 2017.01.07, 2017.05.16
        // The logic of checking the info user group and currentUserCanRead is
        // confusing here. I am not sure exactly what is supposed to happen.
        // Figure it out and document in comments. Hint: I feel like the info
        // should go away and possibly be integrated in to currentUserCanRead.
        if (!ColbyUser::current()->isOneOfThe($info->userGroup)) {
            $response->message = "You do not have permission to read the spec with ID: {$ID}.";
            $response->wasSuccessful = false;
        } else if (CBModels::currentUserCanRead($spec)) {
            $response->spec = $spec;
            $response->wasSuccessful = true;
        } else {
            $response->message = "You do not have permission to read the spec with ID: {$ID}.";
            $response->wasSuccessful = false;
        }

        done:

        $response->send();
    }

    /**
     * @return stdClass
     */
    static function fetchSpecForAjaxPermissions() {
        return (object)['group' => 'Public'];
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
     * Removes the oldest versions, but not the current version, of a model.
     * This function is used clear out unneeded previous versions of a model.
     *
     * @param [hex160] $IDs
     * @param timestamp $maxTimestamp
     *      Previous versions of the models with timestamps less than
     *      $maxTimestamp will be deleted.
     *
     * @return null
     */
    static function removeOldestVersions($IDs, $maxTimestamp) {
        $IDsAsSQL = CBHex160::toSQL($IDs);
        $maxTimestampAsSQL = intval($maxTimestamp);

        $SQL = <<<EOT

            DELETE      `v`
            FROM        `CBModels` AS `m`
            INNER JOIN  `CBModelVersions` as `v`
            ON          `m`.`ID` = `v`.`ID`
            WHERE       `m`.`ID` IN ({$IDsAsSQL}) AND
                        `m`.`version` != `v`.`version` AND
                        `v`.`timestamp` < {$maxTimestampAsSQL}

EOT;

        Colby::query($SQL);
    }

    /**
     * Removes previous versions, but not the current version, of a model. This
     * function is used clear out unneeded previous versions of a model. This is
     * called at the end of CBModels::save().
     *
     * @param [hex160] $IDs
     * @param timestamp $minTimestamp
     *      Previous versions of the models with timestamps greater than
     *      $minTimestamp will be deleted. Passing a value of `0` to this
     *      parameter will remove all versions except for the current version.
     *
     * @return null
     */
    static function removePreviousVersions($IDs, $minTimestamp) {
        $IDsAsSQL = CBHex160::toSQL($IDs);
        $minTimestampAsSQL = intval($minTimestamp);

        $SQL = <<<EOT

            DELETE      `v`
            FROM        `CBModels` AS `m`
            INNER JOIN  `CBModelVersions` as `v`
            ON          `m`.`ID` = `v`.`ID`
            WHERE       `m`.`ID` IN ({$IDsAsSQL}) AND
                        `m`.`version` != `v`.`version` AND
                        `v`.`timestamp` > {$minTimestampAsSQL}

EOT;

        Colby::query($SQL);
    }

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

    /**
     * @return null
     */
    static function revertForAjax() {
        $response = new CBAjaxResponse();
        $ID = $_POST['ID'];
        $version = $_POST['version'];

        CBModels::revert($ID, $version);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function revertForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
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
     * @NOTE How model ID is determined:
     *      - The class specToModel function can set the ID value on the model
     *        by either calculating it or copying it. Potentially no ID value
     *        needs to be set on the spec in this case, and if it is that ID
     *        value will be ignored.
     *      - If specToModel returns a model with no ID value, the ID value on
     *        the spec must be set and will be used.
     *
     * @NOTE Reserved and required properties:
     *      - created: The model will have its created property set to the
     *        timestamp when the model was first saved.
     *      - modified: The model will have its modified property set to the
     *        timestamp of this save.
     *      - version: Both the spec and model will have their version
     *        properties set to the version assigned for this save.
     *
     *      It's a goal to move these properties off the spec and model objects
     *      to a meta object eventually.
     *
     * @param [{stdClass}] $specs
     *  All of the specs must have the same class name. For specs of different
     *  classes make multiple calls.
     *
     * @param bool $force
     *  Use this to disable version checking and force the model save. This
     *  should be used rarely and cautiously. Model import from CSV uses it.
     *  CBModels::revert() also uses it.
     *
     * @return null
     */
    static function save(array $specs, $force = false) {
        if (empty($specs)) { return; }

        if (empty($specs[0]->className)) {
            throw new Exception(__METHOD__ . ' The first spec does not have its `className` propery set.');
        } else {
            $sharedClassName = $specs[0]->className;
        }

        $modified = time();

        $tuples = array_map(function ($spec) use ($sharedClassName) {
            $model = CBModel::specToModel($spec);

            if ($model->className !== $sharedClassName) {
                throw new Exception('All specs must have the same className.');
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

        if (is_callable($function = "{$sharedClassName}::modelsWillSave")) {
            call_user_func($function, $tuples);
        }

        CBModels::saveToDatabase($tuples);

        /* Remove versions less than 10 minutes old. */
        CBModels::removePreviousVersions($IDs, $modified - (60 * 10));

        /* Remove versions more than 30 days old */
        CBModels::removeOldestVersions($IDs, $modified - (60 * 60 * 24 * 30));
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
     * @return null
     */
    static function saveForAjax() {
        // header("HTTP/1.0 404 Not Found"); return;

        $response = new CBAjaxResponse();
        $spec = json_decode($_POST['specAsJSON']);

        if (CBModels::currentUserCanWrite($spec)) {
            Colby::query('START TRANSACTION');

            try {
                CBModels::save([$spec]);
                Colby::query('COMMIT');
                $response->wasSuccessful = true;
            } catch (Exception $exception) {
                Colby::query('ROLLBACK');
                throw $exception;
            }
        } else {
            // Unlike fetchSpecForAjax, an unsuccessful save does mark the
            // response as not successful. Not being able to write is something
            // the end user should be explicitly notified about.
            $ID = empty($spec->ID) ? 'no ID specified' : $spec->ID;
            $response->message = "You do not have permissions to write the spec with ID: {$ID}";
            $response->wasSuccessful = false;
        }

        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function saveForAjaxPermissions() {
        return (object)['group' => 'Public'];
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
     * @deprecated use CBModel::value()
     *
     * @return string
     */
    public static function specToTitle(stdClass $spec) {
        return CBModel::value($spec, 'title', null, 'trim');
    }
}
