<?php

/**
 * @NOTE 2021_04_20
 *
 *      This is the first table in Colby to follow the new "unique words"
 *      pattern where every item such as a class, property, table, or column
 *      has a globally unique name or "unique word". The concept of unique
 *      words was developed to enable more reliable searching for items
 *      without producing false positives.
 */
final class
CBNotificationTable {

    /* -- functions -- */



    /**
     * @return void
     */
    static function
    create(
    ): void {
        $SQL = <<<EOT

            CREATE TABLE
            IF NOT EXISTS
            CBNotification_table (

                CBNotification_CBID_column
                BINARY(20)
                NOT NULL,

                CBNotification_targetUserCBID_column
                BINARY(20),

                CBNotification_targetUserGroupCBID_column
                BINARY(20),



                PRIMARY KEY (
                    CBNotification_CBID_column
                ),

                KEY
                CBNotification_targetUserCBID_key (
                    CBNotification_targetUserCBID_column
                ),

                KEY
                CBNotification_targetUserGroupCBID_key (
                    CBNotification_targetUserGroupCBID_column
                ),

            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;
    }
    /* create() */

}
