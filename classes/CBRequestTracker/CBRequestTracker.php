<?php

final class CBRequestTracker {

    /**
     * @return null
     */
    public static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBRequests` (
                `ID`                BINARY(20) NOT NULL,
                `clientID`          BINARY(20),
                `modelAsJSON`       LONGTEXT NOT NULL,
                `pathname`          VARCHAR(100),
                `requested`         BIGINT NOT NULL,
                `requestedYear`     MEDIUMINT NOT NULL,
                `requestedMonth`    TINYINT NOT NULL,
                `requestedDay`      TINYINT NOT NULL,
                PRIMARY KEY (`ID`),
                KEY `requested` (`requested`),
                KEY `year_month_day_requested` (`requestedYear`, `requestedMonth`, `requestedDay`, `requested`),
                KEY `clientID_requested` (`clientID`, `requested`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    public static function trackForAjax() {
        $ID = $_POST['requestID'];
        $IDAsSQL = CBHex160::toSQL($ID);
        $clientID = $_POST['clientID'];
        $clientIDAsSQL = CBHex160::toSQL($clientID);

        $model = CBModels::modelWithClassName('CBRequest');
        $model->location = json_decode($_POST['location']);
        $model->navigator = json_decode($_POST['navigator']);
        $modelAsJSONAsSQL = CBDB::stringToSQL(json_encode($model));

        $requested = time();
        $requestedYear = (int)gmdate('Y', $requested);
        $requestedMonth = (int)gmdate('n', $requested);
        $requestedDay = (int)gmdate('j', $requested);

        if (isset($model->location->pathname)) {
            $pathnameAsSQL = CBDB::stringToSQL($model->location->pathname);
        }

        $SQL = <<<EOT

            INSERT INTO `CBRequests`
            VALUES (
                {$IDAsSQL},
                {$clientIDAsSQL},
                {$modelAsJSONAsSQL},
                {$pathnameAsSQL},
                {$requested},
                {$requestedYear},
                {$requestedMonth},
                {$requestedDay}
            )

EOT;

        Colby::query($SQL);
    }

    /**
     * @return stdClass
     */
    public static function trackForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }
}
