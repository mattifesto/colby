<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Models',
                                                  'Developer tools for creating and editing models.',
                                                  'admin');

?>

<section>
    <h1>Built in models</h1>

    <?php

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,model,*.data');

    displayModels($absoluteDataFilenames);

    ?>

    <h1>Site specific models</h1>

    <?php

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,model,*.data');

    displayModels($absoluteDataFilenames);

    ?>

    <div><a href="<?php echo "{$_SERVER['REQUEST_URI']}/edit/"; ?>">Create a new model</a></div>
</section>

<?php

$page->end();

/**
 * @return void
 */
function displayModels($absoluteDataFilenames)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/model,([^,]*).data$/', $absoluteDataFilename, $matches);

        $modelId = $matches[1];

        $editURL = COLBY_SITE_URL . "/developer/models/edit/?model-id={$modelId}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <section style="margin-bottom: 2.0em;">
            <h1 style="font-size: 1.5em;"><?php echo $data->nameHTML; ?></h1>
            <p class="hash"><?php echo $modelId; ?>
            <p><?php echo $data->descriptionHTML; ?>
            <p><a href="<?php echo $editURL; ?>">edit</a>
        </section>

        <?php
    }
}
