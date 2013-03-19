<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Models';
$page->descriptionHTML = 'Developer tools for creating and editing models.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

?>

<main>

    <div><a href="<?php echo "{$_SERVER['REQUEST_URI']}/edit/"; ?>">Create a new model</a></div>

    <?php

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,model,*.data');

    displayModels($absoluteDataFilenames, 'colby');

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,model,*.data');

    displayModels($absoluteDataFilenames, 'site');

    ?>

</main>

<?php

done:

$page->end();

/**
 * @return void
 */
function displayModels($absoluteDataFilenames, $type)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/model,([^,]*).data$/', $absoluteDataFilename, $matches);

        $modelId = $matches[1];

        $editURL = COLBY_SITE_URL . "/developer/models/edit/?model-id={$modelId}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <section class="header-metadata-description">
            <h1><?php echo $data->nameHTML; ?></h1>
            <div class="metadata">
                <a href="<?php echo $editURL; ?>">edit</a>
                <span class="hash"><?php echo $modelId; ?></span>
                <span><?php echo $type; ?></span>
            </div>
            <div class="description formatted-content"><?php echo $data->descriptionHTML; ?></div>
        </section>

        <?php
    }
}
