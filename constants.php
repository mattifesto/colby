<?php

/**
 * 2014.08.02
 * This file is loaded almost immediately by `Colby.php`. Assume no other
 * constants are available. Many of the constants and concepts in this file
 * have been deprecated. When their transition is complete consider removing
 * this file and moving what remains directly into the `Colby::initialize`
 * function.
 */

define('CBSystemDirectory',             __DIR__);
define('CBSiteDirectory',               $_SERVER['DOCUMENT_ROOT']);

/**
 * Colby will most likely support web pages of multiple types forever, however
 * the sectioned page is the primary page type because of its flexibility.
 * The name and schema for this page type is "CBPage".
 *
 * Any desire for a new page type should be weighed against the simpler
 * option of creating new custom sections, which is the suggested method of
 * extensibility.
 *
 * The correct naming for page types is (note singular):
 *
 *      CBPageTypeID
 *      CBFunnyPageTypeID
 *      MDSimplePageTypeID
 */

define('CBPageTypeID', '89fe3a7d77424ba16c5101eeb0448c7688547ab2');


/**
 * Deprecated constants
 */

define('COLBY_SYSTEM_DIRECTORY',        __DIR__);
define('COLBY_SITE_DIRECTORY',          $_SERVER['DOCUMENT_ROOT']);
