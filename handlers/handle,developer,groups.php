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

$groups = Colby::findDocumentGroups();

?>

<main>

    <h1>Document Groups</h1>

    <div><a href="<?php echo COLBY_SITE_URL . "/developer/groups/edit/?location="; ?>">Create a new group</a></div>

    <?php

    foreach ($groups as $group)
    {
        $editURL = COLBY_SITE_URL . "/developer/groups/edit/?location={$group->location}&group-id={$group->id}";

        ?>

        <section class="header-metadata-description">
            <h1><?php echo $group->metadata->nameHTML; ?></h1>
            <div class="metadata">
                <a href="<?php echo $editURL; ?>">edit</a>
                <span class="hash"><?php echo $group->id; ?></span>
                <span>location: /<?php echo $group->location; ?></span>
                <span>stub: <?php echo $group->metadata->stub; ?></span>
            </div>
            <div class="description formatted-content"><?php echo $group->metadata->descriptionHTML; ?></div>
        </section>

        <?php
    }

    ?>

</main>

<?php

done:

$page->end();
