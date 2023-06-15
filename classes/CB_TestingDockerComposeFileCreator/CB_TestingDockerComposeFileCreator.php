<?php

final class
CB_TestingDockerComposeFileCreator
{
    // -- functions



    /**
     * @return string
     */
    static function
    create(
        int $mysqlPort,
        int $websitePort,
    ): string
    {
        $mysqlRootPassword =
        CBDB::generateDatabasePassword();

        $mysqlUserUsername =
        CBDB::generateDatabaseUsername();

        $mysqlUserPassword =
        CBDB::generateDatabasePassword();



        $my =
        <<<EOT

        services:


            db:
                environment:
                    MYSQL_ROOT_PASSWORD: "$mysqlRootPassword"
                    MYSQL_USER: "$mysqlUserUsername"
                    MYSQL_PASSWORD: "$mysqlUserPassword"
                    MYSQL_DATABASE: thedatabase
                image: mysql
                ports:
                    - "$mysqlPort:3306"
                restart: always


            web:
                image: php:8.0-apache
                ports:
                    - "$websitePort:80"

        EOT;

        return $my;
    }
    // create()

}
