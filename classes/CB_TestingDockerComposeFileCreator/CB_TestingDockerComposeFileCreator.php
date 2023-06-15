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
                    - "51635:3306"
                restart: always


            web:
                container_name: web_container_23
                image: php:8.0-apache
                ports:
                    - "8082:80"

        EOT;

        return $my;
    }
    // go()

}
