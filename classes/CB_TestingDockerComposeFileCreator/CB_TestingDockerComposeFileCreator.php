<?php

/**
 * @TODO 20230709
 * Matt Calkins
 *
 *      I remove the one use of this class today. I don't want to remove it yet
 *      because I'm still working on related changes. This file should probably
 *      be removed or deprecated then removed.
 */
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

                        ENV CB_DISABLE_SSL true

                        RUN apt-get update

                        RUN apt-get install -y \
                            libfreetype6-dev \
                            libjpeg62-turbo-dev \
                            libpng-dev \
                            libwebp-dev \
                            zlib1g-dev

                        RUN docker-php-ext-configure gd \
                            --with-freetype \
                            --with-jpeg \
                            --with-webp

                        RUN docker-php-ext-install \
                            gd \
                            mysqli

                        RUN a2enmod rewrite

                ports:
                    - "$websitePort:80"
                restart: always

        EOT;

        return $my;
    }
    // create()

}
