<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'MySQL';
$page->descriptionHTML = 'Tools to backup the MySQL database used by the site.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

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

done:

$page->end();
