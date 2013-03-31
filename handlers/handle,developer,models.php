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

    <div><a href="<?php echo "{$_SERVER['REQUEST_URI']}/edit/"; ?>">Create a new document type</a></div>

    <?php

    foreach ($documentGroups as $documentGroup)
    {
        ?>

        <h1><?php echo $documentGroup->metadata->nameHTML; ?></h1>

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
