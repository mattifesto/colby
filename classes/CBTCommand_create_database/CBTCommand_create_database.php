<?php

final class
CBTCommand_create_database {

    /* -- cbt interfaces -- */



    /**
     * @return void
     */
    static function
    cbt_execute(
    ): void {
        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        if ($configurationSpec === null) {
            echo (
                "There is no configuration file. " .
                "Run 'cbt update-configuration'\n"
            );

            exit(1);
        }

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
        identified with caching_sha2_password by
        '{$databasePassword}';

        grant all on
        {$databaseName}.*
        to
        {$databaseUsername}@localhost;

        flush privileges;

        EOT;

        file_put_contents(
            cbsitedir() . '/../create_database.sql',
            $SQL
        );

        echo <<<EOT

            Now run:

                sudo mysql < create_database.sql


        EOT;
    }
    /* cbt_execute() */

}
