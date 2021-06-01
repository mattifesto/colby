<?php

final class CBTCommand_create_database {

    /* -- cbt interfaces -- */



    /**
     * @return void
     */
    static function
    cbt_execute(
    ): void {
        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        $databaseName = CB_Configuration::getDatabaseName(
            $configurationSpec
        );

        $databaseUsername = CB_Configuration::getDatabaseUsername(
            $configurationSpec
        );

        $databasePassword = CB_Configuration::getDatabasePassword(
            $configurationSpec
        );

        $SQL = <<<EOT

        create database
        {$databaseName};

        create user
        {$databaseUsername}@localhost
        identified with mysql_native_password by
        '{$databasePassword}';

        grant all on
        {$databaseName}.*
        to
        {$databaseUsername}@localhost;

        flush privileges;

        EOT;

        file_put_contents(
            cbsitedir() . '/../create-database.sql',
            $SQL
        );

        echo <<<EOT

            Now run:

                sudo mysql < create_database.sql


        EOT;
    }
    /* cbt_execute() */

}
