<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('MySQL Backup');
CBHTMLOutput::setDescriptionHTML('Backup the MySQL database.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,developer,mysql.js');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'develop';
$spec->selectedSubmenuItemName  = 'mysql';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

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

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
