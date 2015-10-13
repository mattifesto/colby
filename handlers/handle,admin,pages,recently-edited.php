<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Recently Edited Pages');
CBHTMLOutput::setDescriptionHTML('A list of the most recently edited pages.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages,recently-edited.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages,recently-edited.js');
CBHTMLOutput::addCSSURL(CBSystemURL . '/javascript/CBPageList.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageList.js');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'recently-edited';

include CBSystemDirectory . '/sections/admin-page-menu.php';

echo '<main></main>';

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
