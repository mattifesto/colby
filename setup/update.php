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
 * These are included so that if a new table is added it only has to be added
 * to the installation script and an upgrade script does not also have to be
 * created. Installation scripts should do nothing if the database elements
 * for the library are already installed.
 */

include CBSystemDirectory . '/setup/install-database.php';

/**
 * This is a hand maintained upgrade list specific to this website. Scripts
 * should be removed and deprecated once all known installations have
 * been upgraded to keep the upgrade process fast and simple.
 */




/**
 * Update the site's schema version number after everything else has been
 * updated successfully.
 */

Colby::setSiteSchemaVersionNumber(COLBY_SITE_VERSION_NUMBER);
