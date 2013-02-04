<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Archive Details', 'View the contents of an archive.', 'admin');

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
