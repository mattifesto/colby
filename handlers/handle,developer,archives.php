<?php

define('COLBY_ARCHIVES_DOCUMENT_ARCHIVE_ID', '5bda1825fe0be9524106061b910fd0b8e1dde0c2');

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archives';
$page->descriptionHTML = 'List, view, delete, and manage archives.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$document = ColbyDocument::documentWithArchiveId(COLBY_ARCHIVES_DOCUMENT_ARCHIVE_ID);

?>

<main>
    <h1>Archives</h1>

    <div style="text-align: center;">
        <progress value="0" max="256" id="progress" style="margin-bottom: 20px;"></progress><br>
        <a class="big-button" onclick="ColbyArchivesExplorer.regenerateDocument();">Regenerate Archives Document</a>
    </div>
</main>

<script src="<?php echo COLBY_URL . '/handlers/handle,developer,archives.js'; ?>"></script>

<?php

done:

$page->end();
