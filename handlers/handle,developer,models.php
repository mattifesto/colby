<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Document Types');
CBHTMLOutput::setDescriptionHTML('Developer tools for creating and editing document types.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'develop';
$spec->selectedSubmenuItemName  = 'types';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

$documentGroups = Colby::findDocumentGroups();

?>

<main>

    <h1 style="margin-bottom: 1em; text-align: center;">Document Types</h1>

    <?php

    $i = 0;

    foreach ($documentGroups as $documentGroup)
    {
        ?>

        <h2><?php echo $documentGroup->nameHTML; ?></h2>

        <div style="font-size: 0.7em;">create a new document type for this group in:

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
                        '/developer/models/edit' .
                        "?location={$libraryDirectory}" .
                        "&document-group-id={$documentGroup->id}";

                    echo '<a href="',
                        $createURL,
                        '" class="spaced">',
                        "/{$libraryDirectory}</a>";
                }

                ?>

            </span>
        </div>

        <div style="margin: 0px 50px 50px;">

            <?php

            $documentTypes = Colby::findDocumentTypes($documentGroup->id);

            foreach ($documentTypes as $documentType)
            {
                $editURL = COLBY_SITE_URL .
                    '/developer/models/edit/' .
                    "?location={$documentType->libraryDirectory}" .
                    "&document-group-id={$documentGroup->id}" .
                    "&document-type-id={$documentType->id}";

                $directoryElementId = "directory" . $i++;

                $directory = "document-groups/{$documentGroup->id}/document-types/{$documentType->id}";

                ?>

                <section class="header-metadata-description">
                    <h1><?php echo $documentType->nameHTML; ?></h1>
                    <div class="metadata">
                        <a href="<?php echo $editURL; ?>">edit</a>
                        <span>
                            <h6>document type id</h6>
                            <div class="hash"><?php echo $documentType->id; ?></div>
                        </span>
                        <span>
                            <h6>library</h6>
                            <div>/<?php echo $documentType->libraryDirectory; ?></div>
                        </span>
                        <span>
                            <h6>created</h6>
                            <div class="time"
                                  data-timestamp="<?php echo $documentType->created * 1000; ?>"></div>
                        </span>
                        <span>
                            <h6>updated</h6>
                            <div class="time"
                                  data-timestamp="<?php echo $documentType->updated * 1000; ?>"></div>
                        </span>
                        <span>
                            <h6>directory
                                <a style="float: right;"
                                   onclick="document.getElementById('<?php echo $directoryElementId; ?>').select();">select</a>
                            </h6>
                            <input type="text"
                                   id="<?php echo $directoryElementId; ?>"
                                   value="<?php echo $directory; ?>"
                                   style="width: 200px;"
                                   readonly>
                        </span>
                    </div>
                    <div class="description formatted-content">
                        <?php echo $documentType->descriptionHTML; ?>
                    </div>
                </section>

                <?php
            }

            ?>

        </div>

        <?php
    }

    ?>

</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
