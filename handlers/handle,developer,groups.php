<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Groups';
$page->descriptionHTML = 'Developer tools for creating and editing groups.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

?>

<main>

    <div><a href="<?php echo COLBY_SITE_URL . "/developer/groups/edit/"; ?>">Create a new group</a></div>

    <?php

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,group,*.data');

    displayGroups($absoluteDataFilenames, 'colby');

    $absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,group,*.data');

    displayGroups($absoluteDataFilenames, 'site');

    ?>

</main>

<?php

done:

$page->end();

/**
 * @return void
 */
function displayGroups($absoluteDataFilenames, $type)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/group,([^,]*).data$/', $absoluteDataFilename, $matches);

        $groupId = $matches[1];

        $editURL = COLBY_SITE_URL . "/developer/groups/edit/?group-id={$groupId}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <section class="header-metadata-description">
            <h1><?php echo $data->nameHTML; ?></h1>
            <div class="metadata">
                <a href="<?php echo $editURL; ?>">edit</a>
                <span class="hash"><?php echo $groupId; ?></span>
                <span><?php echo $type; ?></span>
            </div>
            <div class="description formatted-content"><?php echo $data->descriptionHTML; ?></div>
        </section>

        <?php
    }
}
