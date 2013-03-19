<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Views';
$page->descriptionHTML = 'Developer tools for creating and editing views.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

?>

<main>

    <div><a href="<?php echo COLBY_SITE_URL . "/developer/views/edit/"; ?>">Create a new view</a></div>

    <?php

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,view,*.data');

    displayViews($absoluteDataFilenames, 'colby');

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,view,*.data');

    displayViews($absoluteDataFilenames, 'site');

    ?>

</main>

<?php

done:

$page->end();

/**
 * @return void
 */
function displayViews($absoluteDataFilenames, $type)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/view,([^,]*).data$/', $absoluteDataFilename, $matches);

        $viewId = $matches[1];

        $editURL = COLBY_SITE_URL . "/developer/views/edit/?view-id={$viewId}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <section class="header-metadata-description">
            <h1><?php echo $data->nameHTML; ?></h1>
            <div class="metadata">
                <a href="<?php echo $editURL; ?>">edit</a>
                <span class="hash"><?php echo $viewId; ?></span>
                <span><?php echo $type; ?></span>
            </div>
            <div class="description formatted-content"><?php echo $data->descriptionHTML; ?></div>
        </section>

        <?php
    }
}
