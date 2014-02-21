<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('MySQL Backup');
CBHTMLOutput::setDescriptionHTML('Backup the MySQL database.');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'develop';
$selectedSubmenuItemID  = 'mysql';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>
    <h1>MySQL</h1>

    <div style="margin: 50px 0px; text-align: center;">
        <progress id="backup-database-progress"
                  value="0"
                  style="width: 100px;"></progress>
    </div>

    <div style="text-align: center;">
        <button onclick="DeveloperMySQL.backupDatabase(this);">Backup Database</button>
    </div>
</main>

<script src="<?php echo Colby::findHandler('handle,developer,mysql.js', Colby::returnURL); ?>"></script>

<?php

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

CBHTMLOutput::render();
