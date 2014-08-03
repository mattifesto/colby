<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Document Groups');
CBHTMLOutput::setDescriptionHTML('Developer tools for creating and editing document groups.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('groups');
$menu->renderHTML();

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
                <span>
                    <h6>created</h6>
                    <div class="time" data-timestamp="<?php echo $documentGroup->created * 1000; ?>"></div>
                </span>
                <span>
                    <h6>updated</h6>
                    <div class="time" data-timestamp="<?php echo $documentGroup->updated * 1000; ?>"></div>
                </span>
            </div>
            <div class="description formatted-content"><?php echo $documentGroup->descriptionHTML; ?></div>
        </section>

        <?php
    }

    ?>

</main>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();
