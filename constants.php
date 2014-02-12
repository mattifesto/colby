<?php

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

define('COLBY_BLOG_POSTS_DOCUMENT_GROUP_ID',        '37151457af40ee706cc23de4a11e7ebacafd0c10');
define('COLBY_BLOG_POST_DOCUMENT_TYPE_ID',          'abb85feaa97ca39b1bdb0e8a29359f1995fdcc8d');
define('CBBlogPostTypeId',                          'abb85feaa97ca39b1bdb0e8a29359f1995fdcc8d');

define('COLBY_PAGES_DOCUMENT_GROUP_ID',             'a3f5d7ead80d4e6cb644ec158a13f3a89a9a0622');
define('CBPagesGroupId',                            'a3f5d7ead80d4e6cb644ec158a13f3a89a9a0622');
define('COLBY_PAGE_DOCUMENT_TYPE_ID',               '01fe006d1aca8e85fc140fb642bb200ed6e31596');
