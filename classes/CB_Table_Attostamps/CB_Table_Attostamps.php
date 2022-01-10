<?php

final class
CB_Table_Attostamps {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * CB_Attostamps_rootModelCBID_column
     *
     *      This column holds the CBID of the root model that has either
     *      reserved or registered this attostamp.
     *
     * CB_Attostamps_reservedAtUnixTimestamp_column
     *
     *      If the attostamp is not yet registered, this column holds the Unix
     *      timestamp at which it was reserved. After 24-hours reserved
     *      timestamps are deleted.
     *
     *      When a model is saved, all of the attostamps associated with the
     *      model's CBID are reserved if they are not already reserved. Then the
     *      attostamps requested by the model are registered (and therefore no
     *      longer reserved). Any existing attostamps that are still reserved
     *      (not requested by the model) will then be deleted in approximately
     *      24-hours.
     *
     * CB_Attostamps_unixTimestamp_column
     * CB_Attostamps_attoseconds_column
     *
     *      These two columns hold the attostamp.
     *
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $SQL = <<<EOT

            CREATE TABLE
            IF NOT EXISTS
            CB_Attostamps_table (

                CB_Attostamps_rootModelCBID_column
                BINARY(20) NOT NULL,

                CB_Attostamps_reservedAtUnixTimestamp_column
                BIGINT,

                CB_Attostamps_unixTimestamp_column
                BIGINT NOT NULL,

                CB_Attostamps_attoseconds_column
                BIGINT NOT NULL,

                PRIMARY KEY (
                    CB_Attostamps_unixTimestamp_column,
                    CB_Attostamps_attoseconds_column
                ),

                INDEX
                CB_Attostamps_rootModelCBID_index (
                    CB_Attostamps_rootModelCBID_column
                )
            )

            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* CBInstall_install() */

}
