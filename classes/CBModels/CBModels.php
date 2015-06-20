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
        $name           = $temporary ? 'CBModelsTemp' : 'CBModels';
        $options        = $temporary ? 'TEMPORARY' : '';
        $SQL            = <<<EOT

            CREATE {$options} TABLE IF NOT EXISTS `{$name}`
            (
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
    public static function fetchModelByIDWithVersion($ID, $version) {
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
    public static function install() {
        include __DIR__ . '/CBModelsInstall.php';
    }

    /**
     * Locks the rows for and fetches the version and created timestamp for a
     * set of IDs in preparation for an update. This will also insert rows for
     * IDs that don't exist.
     */
    private static function selectInitialDataForUpdateByID(array $IDs, $modified) {
        $IDsAsSQL   = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

            SELECT  LOWER(HEX(`ID`)), `created`, `version`
            FROM    `CBModels`
            WHERE   `ID` IN ({$IDsAsSQL})
            FOR UPDATE

EOT;

        $initialDataByID = CBDB::SQLToObjects($SQL, ['keyField' => 'ID']);

        if (count($initialDataByID) != count($IDs)) {
            $newIDs = array_diff($IDs, array_keys($initialDataByID));

            foreach ($newIDs as $ID) {
                $initialDataByID[$ID] = (object)['created' => $modified, 'version' => 0];
            }
        }

        return $initialDataByID;
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

        $IDs                = array_map(function ($spec) { return $spec->ID; }, $specs);
        $modified           = time();
        $initialDataByID    = CBModels::selectInitialDataForUpdateByID($IDs, $modified);

        array_walk($specs, function($spec) use ($initialDataByID) {
            $specVersion    = isset($spec->version) ? $specVersion : 0;
            $tableVersion   = $initialDataByID[$spec->ID]->version;

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

        array_walk($tuples, function($tuple) use ($initialDataByID, $modified) {
            $ID                     = $tuple->spec->ID;
            $tuple->model->ID       = $ID;
            $title                  = isset($tuple->spec->title) ? $tuple->spec->title : '';
            $tuple->spec->created   = $tuple->model->created    = $initialDataByID[$ID]->created;
            $tuple->spec->modified  = $tuple->model->modified   = $modified;
            $tuple->spec->title     = $tuple->model->title      = $title;
            $tuple->spec->version   = $tuple->model->version    = $initialDataByID[$ID]->version + 1;
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
        /* 1: CBModelVersions */

        $values     = array_map(function($tuple) {
            $IDAsSQL                = CBHex160::toSQL($tuple->spec->ID);
            $modelAsJSONAsSQL       = CBDB::stringToSQL(json_encode($tuple->model));
            $specAsJSONAsSQL        = CBDB::stringToSQL(json_encode($tuple->spec));

            return "({$IDAsSQL}, {$tuple->spec->version}, {$modelAsJSONAsSQL}, {$specAsJSONAsSQL}, {$tuple->spec->modified})";
        }, $tuples);
        $values     = implode(',', $values);

        Colby::query("INSERT INTO `CBModelVersions` VALUES {$values}");

        /* 2: CBModels */

        $values     = array_map(function($tuple) {
            $IDAsSQL                = CBHex160::toSQL($tuple->spec->ID);
            $classNameAsSQL         = CBDB::stringToSQL($tuple->spec->className);
            $titleAsSQL             = CBDB::stringToSQL($tuple->spec->title);

            return "({$IDAsSQL}, {$classNameAsSQL}, {$tuple->spec->created}, {$tuple->spec->modified}, {$titleAsSQL}, {$tuple->spec->version})";
        }, $tuples);
        $values     = implode(',', $values);

        CBModels::createModelsTable(/* temporary: */ true);
        Colby::query("INSERT INTO `CBModelsTemp` VALUES {$values}");

        $SQL = <<<EOT

            UPDATE  `CBModels`      AS `m`
            JOIN    `CBModelsTemp`  AS `t` ON `m`.`ID` = `t`.`ID`
            SET     `m`.`className` = `t`.`className`,
                    `m`.`modified`  = `t`.`modified`,
                    `m`.`title`     = `t`.`title`,
                    `m`.`version`   = `t`.`version`

EOT;

        Colby::query($SQL);

        $SQL = <<<EOT

            INSERT INTO `CBModels`
            SELECT      `ID`, `className`, `created`, `modified`, `title`, `version`
            FROM        `CBModelsTemp`
            WHERE       `version` = 1

EOT;

        Colby::query($SQL);
    }
}
