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

    private static $versionsByID = null;

    /**
     * @return [<int>]
     */
    private static function fetchCreatedTimestampsForIDs(array $IDs) {
        $IDsAsSQL   = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

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
     * @return {stdClass} | false
     */
    public static function fetchModelForID($ID) {
        $models = self::fetchModelsForIDs([$ID]);

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
    public static function fetchModelsForIDs(array $IDs) {
        $IDsAsSQL   = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

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
    public static function fetchModelWithVersion($ID, $version) {
        $IDAsSQL        = CBHex160::toSQL($ID);
        $versionAsSQL   = (int)$version;
        $SQL            = <<<EOT

            SELECT  `modelAsJSON`
            FROM    `CBModelVersions`
            WHERE   `ID`        = {$IDAsSQL} AND
                    `version`   = {$versionAsSQL}

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
    public static function fetchSpecForID($ID, $args) {
        $specs = CBModels::fetchSpecsForIDs([$ID], $args);

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
    public static function fetchSpecsForIDs(array $IDs, $args) {
        $createSpecForIDCallback = null;
        extract($args, EXTR_IF_EXISTS);

        $IDsAsSQL   = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

            SELECT  LOWER(HEX(`m`.`ID`)), `v`.`specAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE   `m`.`ID` IN ($IDsAsSQL)

EOT;
        $specs      = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

        if (is_callable($createSpecForIDCallback)) {
            $existingIDs    = array_map(function($spec) { return $spec->ID; }, $specs);
            $newIDs         = array_diff($IDs, $existingIDs);
            $newSpecs       = array_map($createSpecForIDCallback, $newIDs);
            $specs          = array_merge($specs, $newSpecs);
        }

        return $specs;
    }

    /**
     * @return null
     */
    private static function insertModels($IDs) {
        $values = array_map(function($ID) {
            $IDAsSQL = CBHex160::toSQL($ID);
            return "($IDAsSQL, 0)";
        }, $IDs);

        $values = implode(',', $values);

        Colby::query("INSERT INTO `CBModels` VALUES {$values}");
    }

    /**
     * @return null
     */
    public static function install() {
        include __DIR__ . '/CBModelsInstall.php';
    }

    /**
     * @deprecated use fetchSpecsForIDs
     *
     * Fetches or creates specs for a set of IDs in preparation for an update
     *
     * This function will cache the versions of the specs it returns and will
     * expect all of those specs to be updated with the next `updateModels`
     * call. No other `ForUpdate` calls are allowed until either `updateModels`
     * or `cancelUpdate` are called.
     *
     * This function is meant to be called during a database transaction.
     *
     * @param {callable} $callback
     *  This callback will be called for each ID that doesn't yet exist in the
     *  database and should return a new spec for that ID.
     */
    public static function makeSpecsForUpdate(array $IDs, callable $callback = null) {
        if (self::$versionsByID) {
            throw new LogicException('Update in progress');
        }

        $IDsAsSQL   = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

            SELECT  LOWER(HEX(`m`.`ID`)), `v`.`specAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE   `m`.`ID` IN ({$IDsAsSQL})

EOT;

        $specsByID      = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
        $versionsByID   = array_map(function($spec) {
            return $spec->version;
        }, $specsByID);

        if (count($versionsByID) != count($IDs)) {
            $newIDs             = array_diff($IDs, array_keys($versionsByID));
            $newVersionsByID    = array_fill_keys(/* keys: */ $newIDs, /* value: */ null);
            $versionsByID       = array_merge($versionsByID, $newVersionsByID);

            foreach ($newIDs as $ID) {
                if ($callback) {
                    $specsByID[$ID] = call_user_func($callback, $ID);
                } else {
                    $specsByID[$ID] = (object)['ID' => $ID];
                }
            }

            self::insertModels($newIDs);
        }

        self::$versionsByID = $versionsByID;

        return $specsByID;
    }

    /**
     * Fetches or creates versions for a set of IDs in preparation for an
     * update
     *
     * This function will cache the versions of the specs it returns and will
     * expect all of those specs to be updated with the next `updateModels`
     * call. No other `ForUpdate` calls are allowed until either `updateModels`
     * or `cancelUpdate` are called.
     *
     * This function is meant to be called during a database transaction. It
     * will insert rows assuming that this action can be rolled back if there
     * is an error.
     */
    public static function makeVersionsForUpdate(array $IDs) {
        if (self::$versionsByID) {
            throw new LogicException('Update in progress');
        }

        $IDsAsSQL   = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

            SELECT  LOWER(HEX(`ID`)), `version`
            FROM    `CBModels`
            WHERE   `ID` IN ({$IDsAsSQL})

EOT;

        $versionsByID = CBDB::SQLToArray($SQL);

        if (count($versionsByID) != count($IDs)) {
            $newIDs         = array_diff($IDs, array_keys($versionsByID));
            $new            = array_fill_keys(/* keys: */ $newIDs, /* value: */ null);
            $versionsByID   = array_merge($versionsByID, $new);

            self::insertModels($newIDs);
        }

        self::$versionsByID = $versionsByID;

        return $versionsByID;
    }

    /**
     * Creates a new model with a class name and optionally an ID.
     */
    public static function modelWithClassName($className, $args = []) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $model              = new stdClass();
        $model->className   = (string)$className;

        if ($ID) {
            $model->ID      = (string)$ID;
        }

        return $model;
    }

    /**
     * Saves a new versions of models. This function should almost always be
     * called inside of a transaction.
     *
     * @param [{stdClass}]  $specs
     *  All of the specs must have the same class name. For specs of different
     *  classes make multiple calls.
     *
     * @return null
     */
    public static function save(array $specs) {
        $className          = $specs[0]->className;

        array_walk($specs, function($spec) use ($className) {
            if ($spec->className !== $className) {
                throw new InvalidArgumentException('specs: All specs must have the same className.');
            }
        });

        $IDs            = array_map(function ($spec) { return $spec->ID; }, $specs);
        $tableVersions  = CBModels::makeVersionsForUpdate($IDs);

        array_walk($specs, function($spec) use ($tableVersions) {
            $specVersion    = isset($spec->version) ? $specVersion : null;
            $tableVersion   = $tableVersions[$spec->ID];

            if ($specVersion !== $tableVersion) {
                throw new RuntimeException('CBModelVersionMismatch');
            }
        });

        $specToModel    = "{$className}::specToModel";
        $specToTuple    = function($spec) use ($specToModel) {
            $tuple          = new stdClass();
            $tuple->spec    = $spec;
            $tuple->model   = call_user_func($specToModel, $spec);
            return $tuple;
        };
        $tuples         = array_map($specToTuple, $specs);

        if (is_callable($function = "{$className}::modelsWillSave")) {
            call_user_func($function, $tuples);
        }

        $createdTimestamps  = CBModels::fetchCreatedTimestampsForIDs($IDs);
        $modified           = time();

        array_walk($tuples, function($tuple) use ($createdTimestamps, $modified) {
            $ID                     = $tuple->spec->ID;
            $created                = isset($createdTimestamps[$ID]) ? $createdTimestamps[$ID] : $modified;
            $tuple->model->ID       = $ID;
            $tuple->spec->created   = $tuple->model->created    = $created;
            $tuple->spec->modified  = $tuple->model->modified   = $modified;
            $tuple->spec->version   = $tuple->model->version    = self::$versionsByID[$ID] + 1;
        });

        CBModels::saveToDatabase($tuples);
    }

    /**
     * @return {stdClass}
     */
    public static function saveForAjax() {
        $response   = new CBAjaxResponse();
        $spec       = json_decode($_POST['specAsJSON']);

        Colby::query('START TRANSACTION');

        try {
            CBModels::save([$spec]);
        } catch (Exception $exception) {
            if ($excption->message === 'CBModelVersionMismatch') {
                $response->wasSuccessful    = false;
                $response->message          = 'This model has been updated since it was fetched.';
                $response->code             = 'version mismatch';
                $response->send();
                return;
            } else {
                throw $exception;
            }
        }

        Colby::query('COMMIT');

        $response->wasSuccessful = true;
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
     * @param [{'spec' : {stdClass}, 'model' : {stdClass}}] $tuples
     */
    private static function saveToDatabase(array $tuples) {
        $versionsByID   = self::$versionsByID;
        $updatedIDs     = array_map(function ($tuple) { return $tuple->spec->ID; }, $tuples);

        if (array_keys($versionsByID) != $updatedIDs) {
            throw new LogicException('The set of updated IDs does not match the list of IDs for update.');
        }

        $values = array_map(function($tuple) use ($versionsByID) {
            $IDAsSQL                = CBHex160::toSQL($tuple->spec->ID);
            $modelAsJSONAsSQL       = CBDB::stringToSQL(json_encode($tuple->model));
            $specAsJSONAsSQL        = CBDB::stringToSQL(json_encode($tuple->spec));

            return "({$IDAsSQL}, {$tuple->spec->version}, {$modelAsJSONAsSQL}, {$specAsJSONAsSQL}, {$tuple->spec->modified})";
        }, $tuples);

        $values = implode(',', $values);

        Colby::query("INSERT INTO `CBModelVersions` VALUES {$values}");

        $IDsAsSQL = CBHex160::toSQL(array_keys($versionsByID));

        Colby::query("UPDATE `CBModels` SET `version` = `version` + 1 WHERE `ID` IN ($IDsAsSQL)");

        self::$versionsByID = null;
    }
}
