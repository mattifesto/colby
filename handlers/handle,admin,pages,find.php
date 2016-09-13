<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Find Pages');
CBHTMLOutput::setDescriptionHTML('Find pages to edit, copy, or delete.');
CBHTMLOutput::requireClassName('CBPagesAdministrationView');

CBAdminPageMenuView::renderModelAsHTML((object)[
    'selectedMenuItemName' => 'pages',
    'selectedSubmenuItemName' => 'find',
]);

echo '<main class="CBUIRoot"></main>';

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
