<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Unpublished Pages');
CBHTMLOutput::setDescriptionHTML('Pages that haven\'t been published.');
CBHTMLOutput::requireClassName('CBPagesAdministrationView');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'unpublished';

include CBSystemDirectory . '/sections/admin-page-menu.php';

echo '<main></main>';

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
