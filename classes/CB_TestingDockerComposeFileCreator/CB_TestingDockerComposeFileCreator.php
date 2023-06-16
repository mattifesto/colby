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
                build:
                    context: .
                    dockerfile_inline: |
                        FROM php:8.0-apache
                        COPY . /var/www/html
                ports:
                    - "$websitePort:80"
                restart: always

        EOT;

        return $my;
    }
    // create()

}
