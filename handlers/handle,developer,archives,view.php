<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archive Details';
$page->descriptionHTML = 'View the contents of an archive.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$archiveId = $_GET['archive-id'];

$dataPath = ColbyArchive::dataPathForArchiveId($archiveId);

$absoluteArchiveFilename = COLBY_DATA_DIRECTORY . "/{$dataPath}/archive.data";

if (!is_file($absoluteArchiveFilename))
{
    ?>

    <p>The archive data file doesn't exist.
    <p><code><?php echo $absoluteArchiveFilename; ?></code>

    <?php

    goto done;
}

$data = unserialize(file_get_contents($absoluteArchiveFilename));

$dataHTML = ColbyConvert::textToHTML(var_export($data, true));

?>

<style>
    pre
    {
        margin-bottom: 50px;
    }

    h6
    {
        margin-bottom: 20px;
    }
</style>

<h6>Data</h6>
<pre><?php echo $dataHTML; ?></pre>

<?php

done:

$page->end();
