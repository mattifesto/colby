<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Groups',
                                                  'Developer tools for creating and editing groups.',
                                                  'admin');

?>

<section>
    <h1>Built in groups</h1>

    <?php

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,group,*.data');

    displayGroups($absoluteDataFilenames);

    ?>

    <h1>Site specific groups</h1>

    <?php

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,group,*.data');

    displayGroups($absoluteDataFilenames);

    ?>

    <div><a href="<?php echo COLBY_SITE_URL . "/developer/groups/edit/"; ?>">Create a new group</a></div>
</section>
<?php

$page->end();

/**
 * @return void
 */
function displayGroups($absoluteDataFilenames)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/group,([^,]*).data$/', $absoluteDataFilename, $matches);

        $editURL = COLBY_SITE_URL . "/developer/groups/edit/?group-id={$matches[1]}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <h1 style="font-size: 1.5em;"><?php echo $data->nameHTML; ?></h1>
        <p><?php echo $data->descriptionHTML; ?>
        <p><a href="<?php echo $editURL; ?>">edit</a>

        <?php
    }
}
