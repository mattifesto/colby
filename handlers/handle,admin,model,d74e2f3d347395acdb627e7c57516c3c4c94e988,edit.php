<?php

/**
 * This is the model editor for a model with a title, subtitle and content.
 */

$modelId = 'd74e2f3d347395acdb627e7c57516c3c4c94e988';
$modelDataFilename = "handle,admin,model,{$modelId}.data";
$modelData = unserialize(file_get_contents(Colby::findHandler($modelDataFilename)));

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = $modelData->nameHTML;
$page->descriptionHTML = $modelData->descriptionHTML;

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$archive = ColbyArchive::archiveFromGetData();

?>

<fieldset>

    <?php include(COLBY_SITE_DIRECTORY . '/colby/snippets/editor-common-fields.php'); ?>

    <section class="control"
             style="margin-top: 10px;">
        <header><label for="content">Content</label></header>
        <textarea id="content" style="height: 400px;"><?php
            echo ColbyConvert::textToHTML($archive->valueForKey('content'));
        ?></textarea>
    </section>

</fieldset>

<?php

done:

$page->end();
