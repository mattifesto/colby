#!/usr/bin/env sh

# 2023-07-08
# Matt Calkins
#
#   This script is a set of commands that I would like to have in the Dockerfile
#   in the development stage. However, they don't currently work correctly when
#   run inside the Dockefile, but do work correctly if run inside the finished
#   development container.
#
#   If we can find a way to get the to work inside the Dockerfile that would be
#   great.



# 2023-10-18
# Matt Calkins
#
#   To remove the current development website to reset or test this script:
#
#       cd /var/www/html
#       rm -r *
#       rm -r .*
#
#   go to Docker Desktop
#   go to the mysql container started with this development environment
#   go to the "Exec" tab which is a command line for that container
#
#   You will need the root password and the database name located in the
#   compose-dev.env file of the Colby Git repository.
#
#       mysql -u root -p
#       (enter <root-password>)
#       drop database <database-name>
#       create database <database-name>



# Run these commands manually in a VSCode terminal before running this script.
#
#   apache2ctl start
#
#       https://github.com/docker-library/php/blob/master/8.0/bullseye/apache/Dockerfile
#
#       The PHP docker image uses "apache2-foreground" to run Apache which has
#       an odd behavior of quitting when "the terminal" is resized. You will
#       cause the terminal to resize if you open this project in VSCode.
#
#       The command "apache2ctl start" does not have this issue. They give a
#       reason for not using this command by default.
#
#   gh auth login (usually not needed)
#
#       When you run this in the VSCode terminal you can accept the default
#       answers to the questions they ask. VSCode will open a web browser on the
#       host (MacOS or Windows) so the process is very easy.
#
#   git config --global user.email "you@example.com"
#   git config --global user.name "Your Name"



# 2023-09-29
# Matt Calkins
#
#       This is how you switch to the user www-data.
#       This script should NOT be run as www-data.
#
#       su www-data -s /bin/bash



cp -r /com.docker.devenvironments.code/dev_website/. /var/www/html

cd /var/www/html

git init .

composer require mattifesto/colby:@dev

./vendor/bin/colby_create_website

./vendor/bin/colby_create_php_ini.sh
