#!/bin/bash

RED=$(tput setaf 1)
OFF=$(tput sgr0)

if [ ! -d colby ]
then
 echo "${RED}This doesn't appear to be a colby web root directory.${OFF}"
 exit
fi

if [ ! -f .htaccess ]
then
    echo "creating '.htaccess'"
    cp colby/setup/htaccess.template .htaccess
else
    echo "${RED}.htaccess already exists, no changes made${OFF}"
fi

if [ ! -f colby-configuration.php ]
then
    echo "creating 'colby-configuration.php'"
    cp colby/setup/colby-configuration.template colby-configuration.php
else
    echo "${RED}colby-configuration.php already exists, no changes made${OFF}"
fi

if [ ! -f index.php ]
then
    echo "creating 'index.php'"
    cp colby/setup/index.template index.php
else
    echo "${RED}index.php already exists, no changes made${OFF}"
fi
