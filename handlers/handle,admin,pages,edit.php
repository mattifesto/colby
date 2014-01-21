<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Page Editor';
$page->descriptionHTML = 'This is an app for editing pages.';

$page->javaScriptURLs[] = COLBY_SYSTEM_URL . '/handlers/handle,admin,pages,edit.js';
$page->javaScriptSnippetFilenames[] = COLBY_SYSTEM_DIRECTORY . '/javascript/snippet-query-variables.php';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

if (isset($_GET['archive-id']))
{
    // load page data model for editing
}
else
{
    $archiveId = sha1(microtime() . rand());

    header("Location: /admin/pages/edit/" .
           "?archive-id={$archiveId}");
}

?>

<main>
    <h1>Page Editor</h1>
</main>

<?php

done:

$page->end();
