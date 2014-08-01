<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('MySQL Backup');
CBHTMLOutput::setDescriptionHTML('Backup the MySQL database.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,developer,mysql.js');

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('mysql');
$menu->renderHTML();

?>

<main>
    <h1 style="text-align: center;">MySQL</h1>

    <div style="margin: 50px 0px; text-align: center;">
        <progress id="backup-database-progress"
                  value="0"
                  style="width: 100px;"></progress>
    </div>

    <div style="text-align: center;">
        <button onclick="DeveloperMySQL.backupDatabase(this);">Backup Database</button>
    </div>
</main>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();
