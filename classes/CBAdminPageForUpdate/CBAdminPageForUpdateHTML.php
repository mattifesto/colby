<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$classURL = CBSystemURL . '/classes/CBAdminPageForUpdate';

CBHTMLOutput::setTitleHTML('Update');
CBHTMLOutput::setDescriptionHTML('Tools to perform site version updates.');
//CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,develop,test-pages.js');
CBHTMLOutput::begin();

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addJavaScriptURL("{$classURL}/CBAdminPageForUpdate.js");

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('update');
$menu->renderHTML();

?>

<main>
    <h1>Update</h1>

    <div style="margin: 50px 0px; text-align: center;">
        <progress id="progress"
                  value="0"
                  style="width: 100px;"></progress>
    </div>

    <div style="text-align: center;">
        <button onclick="ColbySiteUpdater.update(this);">Update Site</button>
    </div>
</main>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();
