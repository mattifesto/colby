<?php

final class CBLog {

    /**
     * @return null
     */
    public static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBLog` (
                `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `category` VARCHAR(80) NOT NULL,
                `message` TEXT NOT NULL,
                `timestamp` BIGINT NOT NULL,
                PRIMARY KEY (`ID`),
                KEY `timestamp` (`timestamp`),
                KEY `category_timestamp` (`category`, `timestamp`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * @param string $category
     * @param string $message
     */
    public static function addMessage($category, $message) {
        $categoryAsSQL = CBDB::stringToSQL($category);
        $messageAsSQL = CBDB::stringToSQL($message);
        $timestampAsSQL = time();

        $SQL = <<<EOT

            INSERT INTO `CBLog` (
                `category`,
                `message`,
                `timestamp`
            ) VALUES (
                {$categoryAsSQL},
                {$messageAsSQL},
                {$timestampAsSQL}
            )

EOT;

        Colby::query($SQL);
    }
}
