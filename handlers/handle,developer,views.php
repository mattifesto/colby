<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Views',
                                                  'Developer tools for creating and editing views.',
                                                  'admin');

?>

<h1>Built in views</h1>

<?php

$absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,view,*.data');

displayViews($absoluteDataFilenames);

?>

<h1>Site specific views</h1>

<?php

$absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,view,*.data');

displayViews($absoluteDataFilenames);

?>

<div><a href="<?php echo COLBY_SITE_URL . "/developer/views/edit/"; ?>">Create a new view</a></div>

<?php

$page->end();

/**
 * @return void
 */
function displayViews($absoluteDataFilenames)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/view,([^,]*).data$/', $absoluteDataFilename, $matches);

        $editURL = COLBY_SITE_URL . "/developer/views/edit/?view-id={$matches[1]}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <h1 style="font-size: 1.5em;"><?php echo $data->nameHTML; ?></h1>
        <p><?php echo $data->descriptionHTML; ?>
        <p><a href="<?php echo $editURL; ?>">edit</a>

        <?php
    }
}
