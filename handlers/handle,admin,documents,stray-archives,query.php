<?php

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archives';
$page->descriptionHTML = 'List, view, delete, and manage archives.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

?>

<main>
    <h1>Query Stray Archives</h1>

    <nav style="text-align: center;">
        <?php renderDocumentsAdministrationMenu(); ?>
    </nav>

    <div>
        <div>
            <label>Field Name: <input type="text" id="query-field-name"></label>
        </div>
        <div>
            <label>Field Value: <input type="text" id="query-field-value"></label>
        </div>
        <div style="text-align: center;">
            <progress value="0" max="256" id="progress" style="margin-bottom: 20px;"></progress><br>
            <a class="big-button" onclick="ColbyStrayArchivesFinder.runQuery();">Query Stray Archives</a>
        </div>
    </div>
</main>

<script src="<?php echo COLBY_URL; ?>/handlers/handle,admin,documents,stray-archives,query.js"></script>
<?php

done:

$page->end();

