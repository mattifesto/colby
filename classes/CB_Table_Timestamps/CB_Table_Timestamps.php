<?php

final class
CB_Table_Timestamps {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * CB_Timestamps_rootModelCBID_column
     *
     *      This column holds the CBID of the root model that has either
     *      reserved or registered this cbtimestamp.
     *
     * CB_Timestamps_reservedAtUnixTimestamp_column
     *
     *      If the cbtimestamp is not yet registered, this column holds the Unix
     *      timestamp at which it was reserved. After 24-hours reserved
     *      timestamps are deleted.
     *
     *      When a model is saved, all of the cbtimestamps associated with the
     *      model's CBID are reserved if they are not already reserved. Then the
     *      cbtimestamps requested by the model are registered (and therefore no
     *      longer reserved). Any existing cbtimestamps that are still reserved
     *      (not requested by the model) will then be deleted in approximately
     *      24-hours.
     *
     * CB_Timestamps_unixTimestamp_column
     * CB_Timestamps_femtoseconds_column
     *
     *      These two columns hold the cbtimestamp.
     *
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $SQL = <<<EOT

            CREATE TABLE
            IF NOT EXISTS
            CB_Timestamps_table (

                CB_Timestamps_rootModelCBID_column
                BINARY(20) NOT NULL,

                CB_Timestamps_reservedAtUnixTimestamp_column
                BIGINT,

                CB_Timestamps_unixTimestamp_column
                BIGINT NOT NULL,

                CB_Timestamps_femtoseconds_column
                BIGINT NOT NULL,

                PRIMARY KEY (
                    CB_Timestamps_unixTimestamp_column,
                    CB_Timestamps_femtoseconds_column
                ),

                INDEX
                CB_Timestamps_rootModelCBID_index (
                    CB_Timestamps_rootModelCBID_column
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
