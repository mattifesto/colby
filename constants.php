<?php

define('CBSystemDirectory',             __DIR__);
define('CBSiteDirectory',               $_SERVER['DOCUMENT_ROOT']);

/**
 * 2014.01.07
 *
 * This is a time of big change, so it's worth documenting those changes in
 * these comments.
 *
 * First, the term "document" is essentially being deprecated. There is no
 * difference between a "document" and a "web page". Since this is a system
 * that manages web pages, the terms "web page", "web pages", "page", or
 * "pages" are the preferred terms now. Class names, function names, variable
 * names, and constant names should all change to reflect this.
 *
 * In general, the term "page(s)" is preferable to "web page(s)" in code,
 * however sometimes "web page(s)" is needed in comments or documentation where
 * the context of "page(s)" may not be entirely clear.
 *
 * Page types are no longer subsets of page groups. Any page group
 * can contain pages of any page type. If any sort of restrictions
 * to this are desired, they can be put into place for the user or the site, but
 * theoretically any page group can contain pages of any page type.
 *
 * The correct naming for page groups is (note plural):
 *
 *      CBSystemPagesGroupID
 *      CBBlogPostsGroupID
 *      MDPressReleasesGroupID
 *
 * The correct naming for page types is (note singular):
 *
 *      CBSectionedPageTypeID
 *      MDSimplePageTypeID
 */

define('CBBlogPostsGroupID',            '37151457af40ee706cc23de4a11e7ebacafd0c10');

/**
 * Colby will most likely support web pages of multiple types forever, however
 * the sectioned page is the primary page type because of its
 * flexibility.
 *
 * Any desire for a new page type should be weighed against the simpler
 * option of creating new custom sections, which is the suggested method of
 * extensibility.
 */

define('CBSectionedPageTypeID',         '89fe3a7d77424ba16c5101eeb0448c7688547ab2');

/**
 * Deprecated constants
 */

define('COLBY_SYSTEM_DIRECTORY',        __DIR__);
define('COLBY_SITE_DIRECTORY',          $_SERVER['DOCUMENT_ROOT']);

define('CBBlogPostsGroupId',            '37151457af40ee706cc23de4a11e7ebacafd0c10');
define('CBSectionedPageTypeId',         '89fe3a7d77424ba16c5101eeb0448c7688547ab2');

define('COLBY_BLOG_POSTS_DOCUMENT_GROUP_ID',        '37151457af40ee706cc23de4a11e7ebacafd0c10');
define('COLBY_BLOG_POST_DOCUMENT_TYPE_ID',          'abb85feaa97ca39b1bdb0e8a29359f1995fdcc8d');
define('CBBlogPostTypeId',                          'abb85feaa97ca39b1bdb0e8a29359f1995fdcc8d');

define('COLBY_PAGES_DOCUMENT_GROUP_ID',             'a3f5d7ead80d4e6cb644ec158a13f3a89a9a0622');
define('CBPagesGroupId',                            'a3f5d7ead80d4e6cb644ec158a13f3a89a9a0622');
define('COLBY_PAGE_DOCUMENT_TYPE_ID',               '01fe006d1aca8e85fc140fb642bb200ed6e31596');
define('CBPageTypeId',                              '01fe006d1aca8e85fc140fb642bb200ed6e31596');
