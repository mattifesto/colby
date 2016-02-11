<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Find Pages');
CBHTMLOutput::setDescriptionHTML('Find pages to edit, copy, or delete.');
CBHTMLOutput::requireClassName('CBPagesAdministrationView');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'find';

include CBSystemDirectory . '/sections/admin-page-menu.php';

echo '<main class="CBUIRoot"></main>';

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
