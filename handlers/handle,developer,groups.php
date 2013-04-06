<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Document Groups';
$page->descriptionHTML = 'Developer tools for creating and editing document groups.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$documentGroups = Colby::findDocumentGroups();

?>

<main>

    <h1 style="margin-bottom: 1em; text-align: center;">Document Groups</h1>

    <div style="font-size: 0.7em;">create a new document group in:

        <span>
            <style scoped>
            a.spaced
            {
                padding: 5px 20px;
                margin-left: 20px;
                border: 1px solid #ffdfbf;
                background-color: #fff7df;
            }
            a.spaced:hover
            {
                background-color: #ffefdf;
            }
            </style>

            <?php

            foreach (Colby::$libraryDirectories as $libraryDirectory)
            {
                $createURL = COLBY_SITE_URL .
                    '/developer/groups/edit' .
                    "?location={$libraryDirectory}";

                echo '<a href="',
                    $createURL,
                    '" class="spaced">',
                    "/{$libraryDirectory}</a>";
            }

            ?>

        </span>
    </div>

    <?php

    foreach ($documentGroups as $documentGroup)
    {
        $editURL = COLBY_SITE_URL .
            '/developer/groups/edit/' .
            "?location={$documentGroup->libraryDirectory}&group-id={$documentGroup->id}";

        ?>

        <section class="header-metadata-description">
            <h1><?php echo $documentGroup->nameHTML; ?></h1>
            <div class="metadata">
                <a href="<?php echo $editURL; ?>">edit</a>
                <span>
                    <h6>document group id</h6>
                    <div class="hash"><?php echo $documentGroup->id; ?></div>
                </span>
                <span>
                    <h6>library</h6>
                    <div>/<?php echo $documentGroup->libraryDirectory; ?></div>
                </span>
                <span>
                    <h6>stub</h6>
                    <div><?php echo $documentGroup->stub; ?></div>
                </span>
            </div>
            <div class="description formatted-content"><?php echo $documentGroup->descriptionHTML; ?></div>
        </section>

        <?php
    }

    ?>

</main>

<?php

done:

$page->end();
