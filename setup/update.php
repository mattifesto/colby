<?php

/**
 * @deprecated 2018.08.30
 *
 *      This file can still be overridden by creating a "setup/update.php" file
 *      in the website directory.
 *
 *      This should not be done. Instead, all installation should be done by
 *      implementing the CBInstall interfaces in classes.
 *
 *      This file will be removed once all websites have been confirmed to use
 *      only CBInstall interface installation code.
 */

CBInstall::install();
