<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Models',
                                                  'Developer tools for creating and editing models.',
                                                  'admin');

?>

<h1>Built in models</h1>

<?php

$absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,model,*.data');

displayPostTypes($absoluteDataFilenames);

?>

<h1>Site specific models</h1>

<?php

$absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,model,*.data');

displayPostTypes($absoluteDataFilenames);

?>

<div><a href="<?php echo "{$_SERVER['REQUEST_URI']}/edit/"; ?>">Create a new model</a></div>

<?php

$page->end();

/**
 * @return void
 */
function displayPostTypes($absoluteDataFilenames)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/model,([^,]*).data$/', $absoluteDataFilename, $matches);

        $editURL = COLBY_SITE_URL . "/development/models/edit/?model-id={$matches[1]}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <h1 style="font-size: 1.5em;"><?php echo $data->nameHTML; ?></h1>
        <p><?php echo $data->descriptionHTML; ?>
        <p><a href="<?php echo $editURL; ?>">edit</a>

        <?php
    }
}
