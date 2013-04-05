<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Document Types';
$page->descriptionHTML = 'Developer tools for creating and editing document types.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$documentGroups = Colby::findDocumentGroups();

?>

<main>

    <h1>Document Types</h1>

    <?php

    foreach ($documentGroups as $documentGroup)
    {
        ?>

        <h1><?php echo $documentGroup->metadata->nameHTML; ?></h1>

        <div>create a new document type for this group in:</div>

        <div style="font-size: 0.7em;">
            <style scoped>
            a.spaced + a.spaced
            {
                margin-left: 20px;
            }
            </style>

            <?php

            foreach (Colby::$libraryDirectories as $libraryDirectory)
            {
                $createURL = COLBY_SITE_URL .
                    '/developer/models/edit' .
                    "?location={$libraryDirectory}" .
                    "&document-group-id={$documentGroup->id}";

                echo '<a href="',
                    $createURL,
                    '" class="spaced">',
                    "/{$libraryDirectory}</a>";
            }

            ?>

        </div>

        <?php

        $documentTypes = Colby::findDocumentTypes($documentGroup->id);

        foreach ($documentTypes as $documentType)
        {
            $editURL = COLBY_SITE_URL .
                '/developer/models/edit/' .
                "?location={$documentGroup->location}" .
                "&document-group-id={$documentGroup->id}" .
                "&document-type-id={$documentType->id}";

            ?>

            <section class="header-metadata-description">
                <h1><?php echo $documentType->metadata->nameHTML; ?></h1>
                <div class="metadata">
                    <a href="<?php echo $editURL; ?>">edit</a>
                    <span class="hash"><?php echo $documentType->id; ?></span>
                    <span>location: /<?php echo $documentType->location; ?></span>
                </div>
                <div class="description formatted-content">
                    <?php echo $documentType->metadata->descriptionHTML; ?>
                </div>
            </section>

            <?php
        }
    }

    ?>

</main>

<?php

done:

$page->end();
