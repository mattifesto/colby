<?php

final class CBTasks {

    /**
     * @param string $className
     * @param string $function
     * @param [stdClass] $args
     *
     * @return null
     */
    public static function add($className, $function, array $argsArray, $priority = 0) {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $functionAsSQL = CBDB::stringToSQL($function);
        $priorityAsSQL = (int)$priority;
        $values = [];

        foreach ($argsArray as $args) {
            $argsAsJSONAsSQL = CBDB::stringToSQL(json_encode($args));
            $values[] = "({$classNameAsSQL},{$functionAsSQL},{$argsAsJSONAsSQL},{$priorityAsSQL})";
        }

        $values = implode(',', $values);
        $SQL = <<<EOT

            INSERT INTO `CBTasks`
            (`className`, `function`, `argsAsJSON`, `priority`)
            VALUES {$values}

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    public static function doTask() {
        $startedAsSQL = time();
        $starter = CBHex160::random();
        $starterAsSQL = CBHex160::toSQL($starter);
        $SQL = <<<EOT

            UPDATE `CBTasks`
            SET `started` = {$startedAsSQL}, `starter` = {$starterAsSQL}
            WHERE `started` IS NULL
            ORDER BY `started`, `priority`, `ID`
            LIMIT 1

EOT;

        Colby::query($SQL);

        if (Colby::mysqli()->affected_rows !== 1) {
            return;
        }

        $SQL = <<<EOT

            SELECT `className`, `function`, `argsAsJSON`
            FROM `CBTasks`
            WHERE `starter` = {$starterAsSQL}

EOT;

        $task = CBDB::SQLToObject($SQL);

        // TODO: Add more detailed error handling.

        if (is_callable($function = "{$task->className}::{$task->function}ForTask")) {
            if (empty($task->argsAsJSON)) {
                $args = null;
            } else {
                $args = json_decode($task->argsAsJSON);
            }
            call_user_func($function, $args);
        } else {
            throw new Exception("The function {$function}() requested by task {$ID} is not callable.");
        }

        Colby::query("DELETE FROM `CBTasks` WHERE `starter` = {$starterAsSQL}");
    }

    /**
     * @return null
     */
    public static function doTaskForAjax() {
        $response = new CBAjaxResponse();

        CBTasks::doTask();

        $priority = CBDB::SQLToValue('SELECT MIN(`priority`) FROM `CBTasks` WHERE `started` IS NULL');

        if ($priority === null) {
            // don't set a timeout because there are no tasks
        } else if ($priority < 0) {
            $response->timeout = 0;
        } else if ($priority < 100) {
            $response->timeout = 5000;
        } else {
            $response->timeout = 10000;
        }

        $response->succeeded = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function doTaskForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @return null
     */
    public static function install() {
        $SQL = <<<EOT

        CREATE TABLE IF NOT EXISTS `CBTasks` (
            `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `argsAsJSON` TEXT,
            `className` VARCHAR(80),
            `function` VARCHAR(80),
            `priority` BIGINT NOT NULL DEFAULT 0,
            `started` BIGINT,
            `starter` BINARY(20) DEFAULT NULL,
            PRIMARY KEY (`ID`),
            KEY `className_started` (`className`, `started`),
            KEY `started_priority_ID` (`started`, `priority`, `ID`),
            KEY `starter` (`starter`)
        )
        ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }
}
