<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Archives', 'List, view, delete, and manage archives.', 'admin');

$archiveDirectories = glob(COLBY_DATA_DIRECTORY . '/*');

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
        preg_match('/([0-9a-f]{40})/', $archiveDirectory, $matches);

        $archiveId = $matches[1];

        $archive = ColbyArchive::open($archiveId);
        $viewArchiveURL = COLBY_SITE_URL . "/admin/archives/view/?archive-id={$archiveId}";
        $titleHTML = isset($archive->rootObject()->titleHTML) ? $archive->rootObject()->titleHTML : '';

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

$page->end();
