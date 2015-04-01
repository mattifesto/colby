<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Import Pages');
CBHTMLOutput::setDescriptionHTML('Import pages by uploading page archive files.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages,import.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,pages,import.js');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'pages';
$spec->selectedSubmenuItemName  = 'import';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<main>
</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
