<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$classURL = CBSystemURL . '/classes/CBAdminPageForUpdate';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Update');
CBHTMLOutput::setDescriptionHTML('Tools to perform site version updates.');
CBHTMLOutput::requireClassName('CBAdminPageForUpdate');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'develop';
$spec->selectedSubmenuItemName  = 'update';

CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?><main class="CBUIRoot"></main><?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
