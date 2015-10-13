<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Unpublished Pages');
CBHTMLOutput::setDescriptionHTML('Pages that haven\'t been published.');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages,unpublished.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages,unpublished.js');
CBHTMLOutput::addCSSURL(CBSystemURL . '/javascript/CBPageList.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageList.js');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'unpublished';

include CBSystemDirectory . '/sections/admin-page-menu.php';

echo '<main></main>';

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
