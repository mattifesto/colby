<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Search for Pages');
CBHTMLOutput::setDescriptionHTML('Search for pages to edit.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages,search.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages,search.js');
CBHTMLOutput::addCSSURL(CBSystemURL . '/javascript/CBPageList.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageList.js');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'search';

include CBSystemDirectory . '/sections/admin-page-menu.php';

echo '<main></main>';

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
