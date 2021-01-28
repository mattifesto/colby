<?php

final class CBTCommand_create_database {

    /* -- cbt interfaces -- */



    /**
     * @return void
     */
    static function
    cbt_execute(
    ): void {
        global $argv;

        if (count($argv) < 3) {
            echo "not enough parameters\n";

            exit(1);
        }

        $strings = CBDB::SQLToArrayOfNullableStrings(
            "select CURRENT_USER();"
        );

        $databaseName = $argv[2];

        echo implode(
            "\n",
            $strings
        ) . "\n";

        if (false) {
            echo "ERROR\n";

            exit(1);
        }
    }
    /* cbt_execute() */

}
