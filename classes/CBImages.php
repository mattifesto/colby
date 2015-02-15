<?php

class CBImages {

    /**
     * @return void
     */
    public static function update() {

        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBImages`
            (
                `ID`        BINARY(20) NOT NULL,
                `created`   BIGINT NOT NULL,
                `modified`  BIGINT NOT NULL,

                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }
}
