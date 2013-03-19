<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archives';
$page->descriptionHTML = 'List, view, delete, and manage archives.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$archiveDirectories = glob(COLBY_DATA_DIRECTORY . '/*/*/*');

?>

<table>
    <style scoped="scoped">
        code
        {
            font-size: 0.75em;
        }
    </style>
    <thead><tr>
        <td></td>
        <td>Archive Id</td>
        <td>Title</td>
    </tr></thead>
    <tbody>

    <?php

    foreach ($archiveDirectories as $archiveDirectory)
    {
        preg_match('/([0-9a-f]{2})\/([0-9a-f]{2})\/([0-9a-f]{36})/', $archiveDirectory, $matches);

        $archiveId = $matches[1] . $matches[2] . $matches[3];

        $archive = ColbyArchive::open($archiveId);
        $viewArchiveURL = COLBY_SITE_URL . "/developer/archives/view/?archive-id={$archiveId}";
        $titleHTML = $archive->valueForKey('titleHTML');

        ?>

        <tr>
            <td><a href="<?php echo $viewArchiveURL; ?>">view</a></td>
            <td><code><?php echo $archiveId; ?></code></td>
            <td><?php echo $titleHTML; ?></td>
        </tr>

        <?php
    }

    ?>

    </tbody>
</table>

<?php

done:

$page->end();
