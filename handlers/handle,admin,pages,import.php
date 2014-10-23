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

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('pages');
$menu->setSelectedSubmenuItemName('import');
$menu->renderHTML();

?>

<main>
</main>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();
