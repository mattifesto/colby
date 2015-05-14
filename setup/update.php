<?php

/**
 * This file will only be used if the site does not have its own
 * `setup/update.php` file.
 *
 * 2014.05.14
 * TODO: This file should be copied to the site's setup directory when
 * the initial file copy installation occurs. Then this file will never be
 * run, but will serve as the correct template and can then be modified as
 * the site needs updates and new libraries.
 *
 * When this file is copied, this comment block should be removed.
 */

/**
 * Library database installation scripts.
 *
 * Each library should a 'setup/install-database.php` file. This file should
 * have a "create if not exists" attitude as it will be run both for
 * installation and update. Updates should be added to the end of this file.
 */

include CBSystemDirectory . '/setup/install-database.php';
